<?php

namespace common\library\module;

use common\library\{
    config\ConfigInterface,
    config\IniManager,
    exceptions\ModuleNameException,
    exceptions\ModuleNotFoundException,
    exceptions\RequiredParamException
};

/**
 * Manager library to manage modules
 *
 * @author Artem Hryhorenko<dev7it@gmail.com>
 */
class ModuleManager extends IniManager implements ModuleManagerInterface
{

    /**
     * Instance of iterator specialized on system modules
     * @var ModuleIteratorInterface
     */
    private $modulesIterator = null;

    /**
     * @inheritDoc
     * @throws RequiredParamException
     */
    public function __construct(ModuleIteratorInterface $moduleIterator, string $filePath, int $mode = ConfigInterface::STRICT)
    {
        parent::__construct($filePath, self::SECTION_MODULES, $mode);

        $this->modulesIterator = $moduleIterator;
    }

    /**
     * @inheritDoc
     * @throws FileWriteException
     */
    public function enableModule(string $moduleName): bool
    {
        if (!$this->haveModule($moduleName)) {
            throw new ModuleNotFoundException("Module '{$moduleName}' couldn't be found");
        }
        return $this->set($this->section, $moduleName, self::STATUS_ENABLED);
    }

    /**
     * @inheritDoc
     * @throws FileWriteException
     */
    public function disableModule(string $moduleName): bool
    {
        if (!$this->haveModule($moduleName)) {
            throw new ModuleNotFoundException("Module '{$moduleName}' couldn't be found");
        }
        return $this->set($this->section, $moduleName, self::STATUS_DISABLED);
    }

    /**
     * @inheritDoc
     */
    public function haveModule(string $moduleName): bool
    {
        if (mb_strpos($moduleName, ',') !== false) {
            throw new ModuleNameException("Only single module name should be supplied, but recieved: {$moduleName}");
        }
        return iterator_count(new ModuleFilterIterator($this->modulesIterator, $moduleName)) > 0;
    }

    /**
     * @inheritDoc
     */
    public function availableModules(string $moduleName, $status): array
    {
        // If there is no $moduleName than there is no module!
        if (empty($moduleName)) {
            throw new ModuleNotFoundException("Module '{$moduleName}' couldn't be found");
        }

        //Get the modules
        if ($moduleName === 'all') {
            $available = $this->allAvailable();
        } else {
            $available = $this->fileIteratorToArray(new ModuleFilterIterator($this->modulesIterator, $moduleName));
        }

        // If no modules found than the wrong name was supplied
        if (count($available) === 0) {
            $errMessage = mb_strpos($moduleName, ',') === false ? "Module '{$moduleName}' couldn't be found" : "Any of modules '{$moduleName}' couldn't be found" ;
            throw new ModuleNotFoundException($errMessage);
        }

        // Make compare for requested module status
        if ($status === self::STATUS_DISABLED) {
            return array_diff($available, $this->allActive());
        } else {
            return array_intersect($available, $this->allActive());
        }
    }

    /**
     * @inheritDoc
     */
    public function allAvailable(): array
    {
        return $this->fileIteratorToArray(new ModuleFilterIterator($this->modulesIterator, null));
    }

    /**
     * @inheritDoc
     */
    public function allActive(): array
    {
        $data = $this->getData();
        $active = [];
        foreach ($data[$this->section] as $module => $activeStatus) {
            if ((int)$activeStatus !== self::STATUS_ENABLED) {
                continue;
            }
            $active[] = $module;
        }
        return $active;
    }

    /**
     * @inheritDoc
     */
    public function moduleStatus(string $moduleName = null): array
    {
        $active = array_flip($this->allActive());
        $modules = $this->fileIteratorToArray(new ModuleFilterIterator($this->modulesIterator, $moduleName));
        if ($moduleName !== null && count($modules) === 0) {
            throw new ModuleNotFoundException("Module '{$moduleName}' couldn't be found");
        }
        $status = [];
        foreach ($modules as $module) {
            $status[$module] = isset($active[$module]) ? true : false;
        }
        return $status;
    }

    /**
     * Return iterator values as array
     * @param ModuleFilterIteratorInterface $iterator
     * @return array
     */
    private function fileIteratorToArray(ModuleFilterIteratorInterface $iterator): array
    {
        $array = [];
        foreach ($iterator as $file) {
            $array[] = $file->getFilename();
        }
        return $array;
    }

}
