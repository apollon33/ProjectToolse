<?php

namespace common\library\module;

use common\library\exceptions\ModuleNameException;

/**
 * Filters {@link ModuleIterator} to contain proper module root folder and can be filtered by name
 *
 * @author Artem Hryhorenko
 * @package ModuleManager
 */
class ModuleFilterIterator extends \FilterIterator implements ModuleFilterIteratorInterface
{
    /**
     * This contains filter options for module name
     * @var array|null
     */
    private $filter = null;

    /**
     * Filters {@see \common\library\config\ModuleIterator} to contain proper module root folder and can be filtered by name
     * @param \common\library\config\ModuleIteratorInterface $iterator
     * @param string|null $filter
     */
    public function __construct(ModuleIteratorInterface $iterator, $filter)
    {
        parent::__construct($iterator);
        $this->filter = $filter === null ? null : explode(',', $filter);
        if ($this->filter !== null && in_array('all', $this->filter)) {
            throw new ModuleNameException("Alias 'all' can not be passed as parameter when another module specified.");
        }
    }

    /**
     * @inheritdoc
     */
    public function accept()
    {
        $module = $this->getInnerIterator()->current();
        return $module->isDir() && ($this->filter === null || $this->doFilter($module->getFilename()));
    }

    /**
     * Perform a check is module name requested in filter
     * @param string $moduleName
     * @return bool
     */
    private function doFilter(string $moduleName): bool
    {
        foreach ($this->filter as $value) {
            if (!empty($value) && strcasecmp($moduleName, $value) === 0) {
                return true;
            }
        }
        return false;
    }

}
