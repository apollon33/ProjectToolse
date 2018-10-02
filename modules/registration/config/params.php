<?php
use modules\registration\models\Registration;

return [
    'params' => [
        'admin_modules' => [
            ['label' => Yii::t('app', 'Registrations'), 'url' => ['/registration'],/* 'badge' => Registration::find()->count()],
            ['label' => Yii::t('app', 'Add Registration'), 'url' => ['/registration/create']*/],
        ],
    ],
];