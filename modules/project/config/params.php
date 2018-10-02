<?php
use modules\project\models\Project;

return [
    'params' => [
        'admin_modules' => [
            ['label' => Yii::t('app', 'Projects'), 'url' => ['/project'], 'badge' => Project::find()->count()],
            ['label' => Yii::t('app', 'Add Project'), 'url' => ['/project/create']],
        ],
    ],
];