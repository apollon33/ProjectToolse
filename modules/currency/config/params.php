<?php
use modules\currency\models\Currency;

return [
    'params' => [
        'admin_modules' => [
            ['label' => Yii::t('app', 'Currency'), 'url' => ['/currency'], 'badge' => Currency::find()->count()],
        ],
    ],
];