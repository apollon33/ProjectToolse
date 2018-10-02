<?php

namespace common\models;

use common\access\AccessInterface;
use Yii;
use yii\base\Model;

use modules\module\models\Module;
use common\behaviors\AccessBehavior;
use common\access\AccessChainsDependencies;
use common\access\AccessManager;
use common\access\AccessRoleApplicant;
use common\access\AccessUserApplicant;
use yii\helpers\ArrayHelper;

class RoleForm extends Model
{
    const PATTERN = '/^[a-zA-Z0-9_-]+$/';

    const IS_NEW_SCENARIO = 'isNew';

    public $name;
    public $description;
    public $isNew;


    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 64],
            ['name', 'match', 'pattern' => self::PATTERN],
            [['description'], 'string'],
            ['name', 'validateRoleExist', 'on' => self::IS_NEW_SCENARIO],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Role Group'),
            'description' => Yii::t('app', 'Description'),
        ];
    }

    /**
     * Validate exist Role.
     *
     * @param $attribute
     * @param $params
     *
     * @return bool
     */
    public function validateRoleExist($attribute, $params)
    {
        $hasError = false;
        if (Yii::$app->authManager->getRole($this->{$attribute})) {
            $hasError = true;
        }
        if ($hasError === true) {
            $this->addError(
                $attribute,
                Yii::t('app', 'A role with this name already exists') . ':' . $this->{$attribute}
            );
        }
    }

    /**
     * @param array $listPermission
     * @param string $required
     * @param int $permission
     *
     * @return array
     */
    public static function permissionInfo(array $listPermission, string $required, int $permission) : array
    {
        $listGroups = [];
        $checked = true;
        if (empty($listPermission[$required])) {
            return [
                'access' => false,
                'checked' => $checked,
            ];
        }
        $accessType = AccessInterface::USER;
        $access = false;

        //list of all groups on the module
        foreach ($listPermission[$required] as $permissions) {
            if ($permission === ((integer)$permissions['permission'] & $permission)) {
                if ((integer)$permissions['access_type'] === AccessInterface::GROUP) {
                    $listGroups[] = $permissions['access_id'];
                    $accessType =  AccessInterface::GROUP;
                    $checked = false;
                }
                $access = true;
            }
        }
        // check for the presence permission on the list of chains
        if (!$access) {
            return [
                'access' => false,
                'checked' => $checked,
            ];
        }

        return [
            'access' => true,
            'checked' => $checked,
            'listGroups' => implode('/', $listGroups),
            'access_type' => $accessType,
        ];
    }

    /**
     * @param array $permissions
     * @param int $userId
     *
     * @return array
     */
    public function rolePermissionsForUser(array $permissions, int $userId) : array
    {
        $accessManager = new AccessManager();
        $listPermission = [];
        $listAccessApplicant = $accessManager->applicantsReceipt($userId);
        foreach ($permissions as $permission) {
            $chain = new AccessChainsDependencies($permission->name);
            while ($chain !== null) {
                if (isset($listPermission[$chain->getUId()])) {
                    $chain = $chain->getUParent();
                    continue;
                }
                foreach ($listAccessApplicant as $itemAccessApplicant) {
                    $result = $accessManager->getPermissions($chain, $itemAccessApplicant);
                    if (!empty($result) && ($result['instance_id'] === $chain->getUId())) {
                        $listPermission[$chain->getUId()][] = $result;
                    }
                }
                $chain = $chain->getUParent();
            }
        }

        return $listPermission;
    }

    /**
     * @param array $permissions
     *
     * @return array
     */
    public function getPermissions(array $permissions) : array
    {
        $chains = [];
        foreach ($permissions as $permission) {
            $chain = new AccessChainsDependencies($permission->name);
            $item = [];
            while ($chain !== null) {
                $chainsId = $chain->getUId();
                $item[$chainsId] = $chainsId;
                $chain = $chain->getUParent();
            }
            sort($item);
            if (!empty($chains[$chainsId])) {
                $chains[$chainsId] = array_unique(ArrayHelper::merge($item, $chains[$chainsId]));
            } else {
                $chains[$chainsId] = $item;
            }

        }
        uksort($chains, function ($a, $b) {
            if ($a === 'core') {
                return -1;
            }
            if ($b === 'core') {
                return 1;
            }

            return $a <=> $b;
        });

        return $chains;
    }

    /**
     * @param array $permissions
     * @param AccessInterface $accessApplicant
     *
     * @return array
     */
    public function rolePermission(array $permissions, AccessInterface $accessApplicant) : array
    {
        $rolePermission = [];
        $accessManager = new AccessManager();
        foreach ($permissions as $permission) {
            $chain = new AccessChainsDependencies($permission->name);
            while ($chain !== null) {
                if (!empty($accessManager->getPermissions($chain, $accessApplicant))) {
                    $rolePermission[] = $accessManager->getPermissions($chain, $accessApplicant);
                }
                $chain = $chain->getUParent();
            }
        }

        return ArrayHelper::index($rolePermission, 'instance_id');
    }

    /**
     * @param array $permissions
     * @param AccessInterface $accessApplicant
     */
    public function removePermission(array $permissions, AccessInterface $accessApplicant)
    {
        $accessManager = new AccessManager();
        foreach ($permissions as $permission) {
            $chain = new AccessChainsDependencies($permission->name);
            while ($chain !== null) {
                $accessManager->removePermissions($chain, $accessApplicant);
                $chain = $chain->getUParent();
            }
        }
    }

    /**
     * @param array $newPermissions
     * @param AccessInterface $accessApplicant
     */
    public function savePermission($newPermissions, AccessInterface $accessApplicant)
    {
        $accessManager = new AccessManager();
        if ($newPermissions) {
            foreach ($newPermissions as $key => $newPermission) {
                $accessManager->assignPermissions(
                    new AccessChainsDependencies($key),
                    $accessApplicant,
                    array_sum($newPermission)
                );
            }
        }

    }
}
