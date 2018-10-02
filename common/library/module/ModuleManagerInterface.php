<?php
namespace common\library\module;

use common\library\exceptions\{
    FileWriteException,
    ModuleNameException,
    ModuleNotFoundException
};

interface ModuleManagerInterface
{
    const SECTION_MODULES = 'modules_enabled';

    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;

    /**
     * @param string $moduleName
     * @return bool
     * @throws ModuleNameException
     * @throws ModuleNotFoundException
     */
    public function enableModule(string $moduleName): bool;

    /**
     * @param string $moduleName
     * @return bool
     * @throws ModuleNameException
     * @throws ModuleNotFoundException
     */
    public function disableModule(string $moduleName): bool;

    /**
     * Perform check is requested module available
     * @param string $moduleName
     * @return bool
     */
    public function haveModule(string $moduleName): bool;

    /**
     * Return array of available modules of required status with name filter if required
     * @param string $moduleName
     * @return array
     * @throws ModuleNotFoundException
     */
    public function availableModules(string $moduleName, $status): array;

    /**
     * Return list of all available modules
     * @return array
     */
    public function allAvailable(): array;

    /**
     * Return list of all active modules
     * @return array
     */
    public function allActive(): array;

    /**
     * Return module status enabled/disabled
     * @param string|null $moduleName
     * @throws ModuleNotFoundException
     */
    public function moduleStatus(string $moduleName = null): array;

}
