<?php

namespace common\access;

/**
 * Interface AccessInterface
 * @package common\access
 */

interface AccessInterface
{

    const USER = 1;
    const GROUP = 2;

    /**
     * returns the prefix to unique identifier
     * @return string
     */
    public function getUPrefix() : string;

    /**
     * returns a unique identifier
     * @return string
     */
    public function getUId() : string;

    /**
     * returns an type grup|user
     * @return int
     */
    public function getUType();

    /**
     * returns the unique identifier of the parent
     * @return AccessInterface
     */
    public function getUParent();

    /**
     *check is he owner
     * @return bool
     */
    public function isUOwner();

    /**
     * return owner of this document
     * @return AccessInterface
     */
    public function getUOwner();

}