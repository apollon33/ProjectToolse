<?php
use modules\client\models\Client;
use modules\client\models\Counterparty;

return [
    'params' => [
        'admin_modules' => [
            ['label' => Yii::t('app', 'Clients'), 'url' => ['/client'], 'badge' => Client::find()->count()],
            ['label' => Yii::t('app', 'Add Client'), 'url' => ['/client/create']],
            ['label' => Yii::t('app', 'Counterparty'), 'url' => ['/client/counterparty/index'], 'badge' => Counterparty::find()->count()],
            ['label' => Yii::t('app', 'Trash'), 'url' => ['/client/trash']],
        ],
    ],
];