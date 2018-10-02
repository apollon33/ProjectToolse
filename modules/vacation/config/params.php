<?php
use modules\vacation\models\Vacation;

return [
    'params' => [
        'admin_modules' => [
            ['label' => Yii::t('app', 'Calendar Vacation'), 'url' => ['/vacation/index']],
            ['label' => Yii::t('app', 'General Vacation'), 'url' => ['/vacation/generalvacation']],
            ['label' => Yii::t('app', 'List Vacation'), 'url' => ['/vacation/table']],
            ['label' => Yii::t('app', 'Add Vacation'), 'url' => ['/vacation/create']],
        ],
    ],
];