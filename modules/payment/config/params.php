<?php
use modules\payment\models\Payment;

return [
    'params' => [
        'admin_modules' => [
            ['label' => Yii::t('app', 'Payments'), 'url' => ['/payment'], 'badge' => Payment::find()->count()],
            ['label' => Yii::t('app', 'Add Payment'), 'url' => ['/payment/create']],
        ],
    ],
];