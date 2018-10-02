<?php

namespace common\access;

/**
 * Class AccessChainsDependencies
 *
 * @package common\access
 */
class AccessChainsDependencies implements AccessInterface
{

    public $dependencies;

    public function __construct(string $chainsDependencies)
    {
        $this->dependencies = $chainsDependencies;
    }

    /**
     * @inheritDoc
     */
    public function getUPrefix() : string
    {
        return $this->dependencies;
    }

    /**
     * @inheritDoc
     */
    public function getUId() : string
    {
        return $this->dependencies;
    }

    /**
     * @inheritDoc
     */
    public function getUType()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getUParent()
    {
        return $this->sliceChainsDependencies($this->dependencies);
    }

    /**
     * @inheritDoc
     */
    public function isUOwner() : bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getUOwner()
    {
        return null;
    }

    /**
     * @param string
     *
     * @return AccessChainsDependencies | null
     */
    private function sliceChainsDependencies(string $chainsDependencies)
    {
        $chainsArray = explode('.', $chainsDependencies);
        unset($chainsArray[count($chainsArray) - 1]);
        $chainsArray = implode(".", $chainsArray);

        return empty($chainsArray) ? null : new AccessChainsDependencies($chainsArray);
    }
}
