<?php

return [
    'params' => [
        'admin_modules' => [
            ['label' => Yii::t('app', 'Statistics'), 'url' => ['/activity/statistics/']],
            ['label' => Yii::t('app', 'Activity List'), 'url' => ['/activity/']/*, 'badge' => Activity::find()->count()*/],
            ['label' => Yii::t('app', 'Add Activity'), 'url' => ['/activity/create']],
        ],
    ],
];