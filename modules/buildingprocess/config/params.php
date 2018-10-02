<?php
use modules\buildingprocess\models\ProcessTemplate;

return [
    'params' => [
        'admin_modules' => [
            ['label' => Yii::t('app', 'Building Process'), 'url' => ['/buildingprocess'], 'badge' => ProcessTemplate::find()->count()],
        ],
    ],
];