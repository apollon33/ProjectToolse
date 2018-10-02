<?php
use modules\actioncalendar\models\Event;

use common\access\AccessManager;

$rootLinks= [
    ['label' => Yii::t('app', 'List Event'), 'url' => ['/actioncalendar/event']],
    ['label' => Yii::t('app', 'Add Event'), 'url' => ['/actioncalendar/create']]
];
return [
    'params' => [
        'admin_modules' => array_merge([
            ['label' => Yii::t('app', 'Action Calendar'), 'url' => ['/actioncalendar'], 'badge' => Event::find()->count()],
        ],(Yii::$app->user->can('actioncalendar.backend.default:' . AccessManager::UPDATE) ? $rootLinks : [])),
    ],
];