<?php

use common\library\{
    config\ConfigInterface,
    module\ModuleIterator,
    module\ModuleIteratorInterface,
    module\ModuleManager
};

if (version_compare(PHP_VERSION, '7.0.0', '<')) {
    throw new Exception("Really?!. Use PHP 7 already!");
}

Yii::setAlias('common', dirname(__DIR__));
Yii::setAlias('frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('modules', dirname(dirname(__DIR__)) . '/modules');
Yii::setAlias('generators', dirname(dirname(__DIR__)) . '/generators');
Yii::setAlias('api', dirname(dirname(__DIR__)) . '/api');
Yii::setAlias('widgets', '@common/widgets');
Yii::setAlias('themes', '@frontend/themes');
Yii::setAlias('file.path', '@backend/web/storage/files');
Yii::setAlias('image.path', '@backend/web/storage/images');
Yii::setAlias('image.url', '/storage/images');
Yii::setAlias('image.default', '/images/default.png');
Yii::setAlias('image.theme', '/themes/images');

// Register to return the same instance rather than creating new one on every get
Yii::$container->set('configManager', new ModuleManager(
    new ModuleIterator(Yii::getAlias('@modules')),
    Yii::getAlias(ModuleIteratorInterface::MODULE_CONFIG_PATH),
    ConfigInterface::ALLOW_CREATE | ConfigInterface::ALLOW_EMPTY
));

Yii::$container->set('common\library\module\ModuleManagerInterface', 'configManager');
Yii::$container->set('common\library\config\ConfigInterface', 'configManager');
