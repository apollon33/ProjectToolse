<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use common\library\module\ModuleManagerInterface;
use common\library\permission\ModulePermission;

class ManagerController extends Controller
{
    const STRUCTURE_METHOD_NAME = 'getPermissionStructure';

    /** @var ModuleManagerInterface  */
    public $configManager;

    /**
     * PermissionController constructor.
     * @param string $id
     * @param \yii\base\Module $module
     * @param ModuleManagerInterface $configManager
     * @param array $config
     */
    public function __construct($id, $module, ModuleManagerInterface $configManager, $config = [])
    {
        $this->configManager = $configManager;
        parent::__construct($id, $module, $config);
    }

    /**
     * @param string $moduleName
     */
    public function actionRegister($moduleName)
    {
        try {
            $modules = $this->configManager->availableModules($moduleName, ModuleManagerInterface::STATUS_DISABLED);
            if (count($modules) === 0) {
                $this->stdout("All specified modules already enabled\n", Console::FG_YELLOW);
                return;
            }

            foreach ($modules as $module) {
                $this->configManager->enableModule($module);
                $this->stdout(Console::renderColoredString("Module '%c{$module}%n' enabled.\n"));
                $permissionStructure = $this->getPermissionStructure($module);
                if ($permissionStructure === null) {
                    continue;
                }
                $auth = Yii::$app->authManager;
                $permission = new ModulePermission($permissionStructure, $auth);
                $permission->register();
                $this->stdout(Console::renderColoredString("Module '%c{$module}%n' permissions %gadded%n.\n"));
            }
        } catch (\Throwable $ex) {
            $this->stdout(Console::renderColoredString('%RError: %n%_' . $ex->getMessage() . "\n"));
        }
    }

    /**
     * @param string $moduleName
     */
    public function actionUnRegister($moduleName)
    {
        try {
            $modules = $this->configManager->availableModules($moduleName, ModuleManagerInterface::STATUS_ENABLED);
            if (count($modules) === 0) {
                $this->stdout("All specified modules already disabled\n", Console::FG_YELLOW);
                return;
            }

            foreach ($modules as $module) {
                $this->configManager->disableModule($module);
                $this->stdout(Console::renderColoredString("Module '%c{$module}%n' disabled.\n"));
                $permissionStructure = $this->getPermissionStructure($module);
                if ($permissionStructure === null) {
                    continue;
                }
                $auth = Yii::$app->authManager;
                $permission = new ModulePermission($permissionStructure, $auth);
                $permission->unRegister();
                $this->stdout(Console::renderColoredString("Module '%c{$module}%n' permissions %rremoved%n.\n"));
            }
        } catch (\Throwable $ex) {
            $this->stdout(Console::renderColoredString('%RError: %n%_' . $ex->getMessage() . "\n"));
        }
    }

    /**
     * @param string $moduleName
     */
    public function actionStatus($moduleName = null)
    {
        try {
            if ($moduleName == 'all') {
                $moduleName = null;
            }
            $modules = $this->configManager->moduleStatus($moduleName);
            if (count($modules) === 0) {
                $this->stdout("There is no available modules\n", Console::FG_YELLOW);
                return;
            }
            foreach ($modules as $module => $status) {
                $this->stdout(Console::renderColoredString($this->moduleStatusMessage($module, $status) . "\n"));
            }
        } catch (\Throwable $ex) {
            $this->stdout(Console::renderColoredString('%RError: %n%_' . $ex->getMessage() . "\n"));
        }
    }

    private function moduleStatusMessage(string $module, bool $status): string
    {
        if ($status === true) {
            $color = 'G';
            $name = 'enabled';
        } else {
            $color = 'R';
            $name = 'disabled';
        }
        return "Module '%c{$module}%n': %{$color}{$name}%n";
    }

    /**
     * @param string $moduleName
     * @return string
     */
    private function getClassName(string $moduleName): string
    {
        return 'modules\\' . $moduleName . '\\Module';
    }

    /**
     * @param string $moduleName
     * @return null|array
     */
    private function getPermissionStructure(string $moduleName)
    {
        $className = $this->getClassName($moduleName);
        $classExist = class_exists($className);
        if (!$classExist) {
            $this->stdout(Console::renderColoredString("Module '%c{$moduleName}%n' definition doesn't exist: '%R{$className}%n'.\n"));
            return null;
        }

        $structureExists = method_exists($className, self::STRUCTURE_METHOD_NAME);
        if (!$structureExists) {
            $this->stdout(Console::renderColoredString("Module '%c{$moduleName}%n' the permission structure does not specified.\n"));
            return null;
        }

        return call_user_func([$className, self::STRUCTURE_METHOD_NAME]);
    }

}
