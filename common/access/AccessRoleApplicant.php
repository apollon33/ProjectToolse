<?php
namespace common\access;

use common\access\AccessRoleInterface;

/**
 * Applicant that represents a group of users
 * @todo This should be redone as extend class of basic applicant
 */
class AccessRoleApplicant implements AccessRoleInterface
{
    /**
     * @var int|string $name
     */
    protected $name;

    /**
     * AccessApplicant constructor.
     * @param int|string $name
     * @param int $type
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * returns a unique identifier
     * @return int|string
     */
    public function getUId() : string
    {
        return $this->name;
    }

    /**
     * returns an object class
     * @return int
     */
    public function getUType()
    {
        return self::GROUP;
    }

    /**
     * returns the unique identifier of the parent object
     * @return AccessInterface
     */
    public function getUParent()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getUPrefix() : string
    {
        return '';
    }

    /**
     *check is he owner
     * @return bool
     */
    public function isUOwner()
    {
        return false;
    }

    /**
     * return owner of this document
     * @return AccessInterface
     */
    public function getUOwner()
    {
        return null;
    }
}