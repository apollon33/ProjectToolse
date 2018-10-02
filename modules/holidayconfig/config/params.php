<?php
use modules\holidayconfig\models\HolidayConfig;

return [
    'params' => [
        'admin_modules' => [
            ['label' => Yii::t('app', 'Holiday Config'), 'url' => ['/holidayconfig'],/* 'badge' => HolidayConfig::find()->count()],*/
            /*['label' => Yii::t('app', 'Add HolidayConfig'), 'url' => ['/holidayconfig/create']*/],
        ],
    ],
];