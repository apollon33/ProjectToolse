<?php
use common\library\config\ManagerInterface;
use common\library\config\ModuleManagerInterface;

/* @var $manager common\library\config\ModuleManagerInterface */
$manager = Yii::$container->get('configManager');

$modules = [];
foreach ($manager->allActive() as $module) {
    $modules[$module] = [
        'class' => 'modules\\' . $module . '\Module',
        'controllerNamespace' => 'modules\\' . $module . '\controllers\api',
        'viewPath' => '@modules/' . $module . '/views/api',
    ];
}

return $modules;