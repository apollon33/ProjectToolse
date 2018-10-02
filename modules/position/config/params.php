<?php
use modules\position\models\Position;

return [
    'params' => [
        'admin_modules' => [
            ['label' => Yii::t('app', 'Positions'), 'url' => ['/position'],/* 'badge' => Position::find()->count()],*/
           /* ['label' => Yii::t('app', 'Add Position'), 'url' => ['/position/create']*/],
        ],
    ],
];