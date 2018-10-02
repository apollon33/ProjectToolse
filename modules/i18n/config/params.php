<?php
use modules\i18n\models\Translation;
use modules\i18n\models\Language;

return [
    'params' => [
        'admin_modules' => [
            ['label' => Yii::t('app', 'Translations'), 'url' => ['/i18n'], 'badge' => Translation::find()->count()],
            ['label' => Yii::t('app', 'Add Translation'), 'url' => ['/i18n/create']],
            ['label' => Yii::t('app', 'Languages'), 'url' => ['/i18n/language/index'], 'badge' => Language::find()->count()],
            ['label' => Yii::t('app', 'Add Language'), 'url' => ['/i18n/language/create']],
        ],
    ],
];
