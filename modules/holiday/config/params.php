<?php
use modules\holiday\models\Holiday;

return [
    'params' => [
        'admin_modules' => [
            ['label' => Yii::t('app', 'Holidays'), 'url' => ['/holiday'], /*'badge' => Holiday::find()->count()],
            ['label' => Yii::t('app', 'Add Holiday'), 'url' => ['/holiday/create']*/],
        ],
    ],
];