<?php
use modules\calendar\models\Calendar;

return [
    'params' => [
        'admin_modules' => [
            ['label' => Yii::t('app', 'Report View'), 'url' => ['/calendar/report'], 'badge' => Calendar::find()->count()],
            ['label' => Yii::t('app', 'General Report'), 'url' => ['/calendar/reportcart']],
            ['label' => Yii::t('app', 'Calendar View'), 'url' => ['/calendar']],
            ['label' => Yii::t('app', 'List View'), 'url' => ['/calendar/list']],
            ['label' => Yii::t('app', 'Add Task'), 'url' => ['/calendar/create']],
        ],
    ],
];