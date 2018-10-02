<?php

namespace common\behaviors;

use Yii;
use yii\db\ActiveRecord;
use yii\base\Behavior;
use common\access\AccessManager;
use common\access\AccessUserApplicant;
use common\access\AccessInterface;

/**
 * Class AccessBehavior
 *
 * @package common\behaviors
 *
 * @property-read AccessUserApplicant $applicant that checked accesses against
 */
class AccessBehavior extends Behavior
{

    /**
     * @var AccessInterface
     */
    private $_applicant;

    /**
     * @var AccessManager|null
     */
    private $accessManager;

    /**
     *
     * @return AccessUserApplicant
     */
    public function getApplicant()
    {
        if ($this->_applicant === null) {
            $this->_applicant = new AccessUserApplicant(Yii::$app->user->id, AccessInterface::USER);
        }
        return $this->_applicant;
    }

    public function setApplicant(AccessInterface $applicant)
    {
        $this->_applicant = $applicant;
    }

    /**
     * Start up initialization
     */
    public function init()
    {
        $this->accessManager = Yii::$app->accessManager;
    }

    /**
     * @param int $permission
     *
     * @return bool
     */
    public function checkPermission($permission)
    {
        return $this->accessManager->checkPermission($this->owner, Yii::$app->user->getIdentity(), $permission);
    }

    /**
     * @return array
     */
    public function getListAllAccessHolders($permission = null)
    {
        return $this->accessManager->listAllAccessHolders($this->owner, $permission);
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        return $this->accessManager->getPermissions($this->owner, $this->getApplicant());
    }

    /**
     * @param $applicant
     */
    public function removePermission()
    {
        $this->accessManager->removePermissions($this->owner, $this->getApplicant());
    }

    /**
     * @param $applicant
     */
    public function removeAllPermission()
    {
        $this->accessManager->removeAllPermissions($this->getApplicant());
    }

    /**
     * @param $sumPermission
     */
    public function assignPermission($sumPermission)
    {
        $this->accessManager->assignPermissions($this->owner, $this->getApplicant(), $sumPermission);
    }
}
