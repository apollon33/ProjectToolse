<?php
use modules\document\models\Document;

$documentRoots = Document::find()->roots()->all();
$rootLinks = [];

return [
    'params' => [
        'admin_modules' => array_merge([
            ['label' => Yii::t('app', 'Documents'), 'url' => ['/document']],
        ], $rootLinks),
    ],
];
