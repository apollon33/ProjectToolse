<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'homeUrl' => '/user/structure',
    'bootstrap' => ['log'],
    'modules' => [],
    'defaultRoute' => 'auth/login',
    'components' => [
        'request' => [
            'baseUrl' => ''
        ],
        'user' => [
            'class' => 'common\library\UserAccessManager',
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['auth/login'],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => YII_DEBUG ? 'error/error' : 'error/index',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                'role' => 'role/index',
                'role/<action:[\w-]+>' => 'role/<action>',
                'user' => 'user/index',
                'user/<action:[\w-]+>' => 'user/<action>',
                '<module:[\w\-]+>' => '<module>/default/index',
                '<module:[\w\-]+>/<id:\d+>/<action:[\w-]+>' => '<module>/default/<action>',
                '<module:[\w\-]+>/<action:[\w-]+>' => '<module>/default/<action>',
                '<module:[\w\-]+>/<controller:\w+>/<id:\d+>/<action:\w+>' => '<module>/<controller>/<action>',
            ],
        ],
        /*'i18n' => [
            'translations' => [
                'app*' => [
                    'basePath' => '@backend/messages',
                ],
            ],
        ],*/
    ],
    'params' => $params,
    'modules' => require(__DIR__ . '/modules.php'),
];
