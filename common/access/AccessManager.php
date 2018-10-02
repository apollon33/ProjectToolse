<?php

namespace common\access;

use common\models\User;
use modules\buildingprocess\models\ProcessInstance;
use modules\document\models\Document;
use common\access\AccessInterface;

use common\access\AccessApplicant;
use common\access\AccessChainsDependencies;
use yii\rbac\CheckAccessInterface;
use yii\db\Query;
use Yii;
use yii\web\IdentityInterface;


/**
 * Class AccessManager
 *
 * @package common\library\permission
 * @todo There is know issue when owner could be stripped of access to his document is there is "restrictive" rule specified on child folder for group this owner belonged
 *
 */
class AccessManager implements CheckAccessInterface
{

    const VIEW = 1 << 0;
    const UPDATE = 1 << 1;
    const CREATE = 1 << 2;
    const DELETE = 1 << 3;
    const ASSIGN =  1 << 4;
    const MANAGE = 1 << 5;

    const CRUD = self::VIEW | self::UPDATE | self::CREATE | self::DELETE;
    const ANY_READ = self::VIEW | self::ASSIGN;
    const ANY_WRITE = self::UPDATE | self:: ASSIGN;
    const ADMIN = self::CRUD | self::MANAGE;

    /**
     * @var string the name of the table storing access document. Defaults to "access_document".
     */
    public $itemTable = '{{%access_permissions}}';

    /**
     * @param int $applicant
     *
     * @return AccessInterface[]
     */
    public function applicantsReceipt (int $applicant) : array
    {
        $role = Yii::$app->authManager->getRolesByUser($applicant);
        $roles = [];
        $roles[] = new AccessUserApplicant($applicant);
        foreach ($role as $item) {
            $roles[] = new AccessRoleApplicant($item->name);
        }
        return $roles;
    }

    /**
     * @param int $identity
     * @param AccessInterface[] $applicant
     *
     * @return array
     */
    private function hasPermission($identity, array $applicant)
    {
        $permissions = [];
        foreach ($applicant as $item) {
            $query = (new Query())
                ->from($this->itemTable)
                ->where([
                    'instance_id' => $identity,
                    'access_type' => $item->getUType(),
                    'access_id'   => $item->getUId(),
                ])->createCommand();

            if ($query->queryScalar()) {
                $permissions[] = $item;
            }
        }
        return $permissions;

    }

    /**
     * @param int $identity
     * @param AccessInterface[] $applicant
     * @param int $permission
     *
     * @return bool
     */
    private function isAllowed($identity, array $applicant, int $permission) : bool
    {
        if (empty($identity) || empty($applicant)) {
            return false;
        }
        $applicantList = array_map(function ($a) {return $a->getUId();}, $applicant);
        $query = (new Query())
            ->from($this->itemTable)
            ->where([
                'instance_id' => $identity,
                'access_id' => $applicantList,
            ])
            ->andWhere(['&', 'permission', $permission]);
        if ($query->exists()) {
            return true;
        }

        return false;
    }


    /**
     * @param AccessInterface $identity
     * @param IdentityInterface $applicant
     * @param int $permission
     *
     * @return bool
     */
    public function checkPermission(AccessInterface $identity, IdentityInterface $applicant, $permission)
    {
        if ($applicant->isSuperAdmin() || $identity->isUOwner()) {
            return true;
        }
        $applicants = $this->applicantsReceipt($applicant->getId());
        $document = $identity;
        while ($document !== null) {
            $documentId = $document->getUId();
            $attachedApplicants = $this->hasPermission($documentId, $applicants);
            if ($this->isAllowed($documentId, $attachedApplicants, $permission)) {
                return true;
            }

            $applicants = array_udiff($applicants, $attachedApplicants, function ($a, $b) {
                    if ($a->getUId() === $b->getUId()) {
                        return 0;
                    }

                    return ($a->getUId() > $b->getUId()) ? 1 : -1;
                }
            );

            $document = $document->getUParent();
        }

        return false;
    }

    /**
     * @param AccessInterface $identity
     * @param AccessInterface $applicant
     *
     * @return array
     */
    public function getPermissions(AccessInterface $identity, AccessInterface $applicant)
    {
        $permissions = [];

        $document = $identity;

        while ($document !== null) {
            $documentId = $document->getUId();
            if ($this->hasPermission($documentId, [$applicant])) {
                $query = (new Query())
                    ->select('*')
                    ->from($this->itemTable)
                    ->where([
                        'instance_id' => $documentId,
                        'access_type' => $applicant->getUType(),
                        'access_id'   => $applicant->getUId(),
                    ])->createCommand();

                if ($query->queryScalar()) {
                    $permissions = $query->queryOne();
                }

                return $permissions;
            }
            $document = $document->getUParent();
        }
    }

    /**
     * @param AccessInterface $identity
     * @param AccessInterface $applicant
     * @param int $permissions
     */
    public function assignPermissions(AccessInterface $identity, AccessInterface $applicant, $permissions)
    {
        $query = (new Query())
            ->from($this->itemTable)
            ->where([
                'instance_id' => $identity->getUId(),
                'access_id'   => $applicant->getUId(),
                'access_type' => $applicant->getUType(),
            ])
            ->createCommand();

        if ($query->queryScalar()) {
            Yii::$app->db->createCommand()->update($this->itemTable,[
                'permission'  => $permissions,
            ], [
                'instance_id' => $identity->getUId(),
                'access_id'   => $applicant->getUId(),
                'access_type' => $applicant->getUType(),
            ])->execute();
        } else {
            Yii::$app->db->createCommand()->insert($this->itemTable, [
                'instance_id' => $identity->getUId(),
                'access_id'   => $applicant->getUId(),
                'access_type' => $applicant->getUType(),
                'permission'  => $permissions,
            ])->execute();
        }
    }

    /**
     * @param AccessInterface $identity
     * @param AccessInterface $applicant
     */
    public function removePermissions(AccessInterface $identity, AccessInterface $applicant)
    {
        Yii::$app->db->createCommand()->delete($this->itemTable, [
            'instance_id' => $identity->getUId(),
            'access_id'   => $applicant->getUId(),
            'access_type' => $applicant->getUType(),
        ])->execute();
    }

    /**
     * @param AccessInterface $applicant
     */
    public function removeAllPermissions(AccessInterface $applicant)
    {
        Yii::$app->db->createCommand()->delete($this->itemTable, [
            'access_id'   => $applicant->getUId(),
            'access_type' => $applicant->getUType(),
        ])->execute();
    }

    /**
     * @param AccessInterface $identity
     * @param int $permission
     *
     * @return array
     */
    public function listAllAccessHolders(AccessInterface $identity, $permission)
    {
        $applicant = [];
        $query = (new Query())
            ->select('*')
            ->from($this->itemTable)
            ->where(['instance_id' => $identity->getUId()])
            ->andFilterCompare('permission', $permission, '&')
            ->createCommand();

        $applicant = $query->queryAll();

        return $applicant;
    }

    /**
     * @param int|string $userId
     * @param string $permissionName
     * @param array $params
     *
     * @return bool
     */
    public function checkAccess($userId, $permissionName, $params = []) : bool
    {
        if ($permissionName === '@' || $permissionName === '?' || empty($userId)) {
            return false;
        }
        $applicant = User::findOne($userId);

        if (empty($applicant)) {
            return false;
        }

        if (is_int($permissionName)) {
            $chain = new PathChain();
        } else {
            $params = explode(':', $permissionName);
            $chain = $params[0];
            $permissionName = $params[1];
        }
        $identity = new AccessChainsDependencies($chain);

        return $this->checkPermission($identity, $applicant, $permissionName);
    }
}