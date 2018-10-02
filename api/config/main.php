<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    //require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php')
    //require(__DIR__ . '/../../backend/config/params-local.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'homeUrl' => '/api',
    'bootstrap' => ['log'],
    'modules' => [],
    'defaultRoute' => 'user/default/index', //TODO
    'components' => [
        'request' => [
            'baseUrl' => '/api',
            'enableCookieValidation' => false,
            'enableCsrfValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',

            ],
        ],
        'response' => [
            'formatters' => [
                'json' => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG,
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ]
        ],
        'user' => [
            'class' => 'common\library\UserAccessManager',
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
            'loginUrl' => false,
            'enableSession' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logVars' => [],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/default/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                'POST login' => 'login/index',
                'GET search' => 'search/index',
                'GET structure' => 'structure/index',
                'GET structure/permission' => 'structure/permission',
                'GET user' => 'user/index',
                'POST user' => 'user/create',
                'GET user/permission' => 'user/permission',
                'GET user/<id:\d+>' => 'user/view',
                'PUT user/restore/<id:\d+>' => 'user/archive-restore',
                'PUT,PATCH user/<id:\d+>' => 'user/update',
                'PUT user/archive/<id:\d+>' => 'user/delete',
                'GET user/archive' => 'user/archive',
                'DELETE user/<id:\d+>' => 'user/archive-delete',

                'GET <module:[\w\-]+>' => '<module>/default/index',
                'GET,HEAD <module:[\w\-]+>/<id:\d+>' => '<module>/default/view',
                'POST <module:[\w\-]+>' => '<module>/default/create',
                'PUT,PATCH <module:[\w\-]+>/<id:\d+>' => '<module>/default/update',
                'DELETE <module:[\w\-]+>/<id:\d+>' => '<module>/default/delete',
                'GET <module:[\w\-]+>/permission' => '<module>/default/permission',
                'GET <module:[\w\-]+>/<id:\d+>/attachment' => '<module>/default/attachment',
            ],
        ],
    ],
    'modules' => require(__DIR__ . '/modules.php'),
    'params' => $params,
];

