<?php

namespace modules\client;

use Yii;
use yii\base\Module as BaseModule;
use common\library\permission\ModulePermission;

/**
 * page module definition class
 */
class Module extends BaseModule
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'modules\client\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
        Yii::configure($this, require(__DIR__ . '/config/params.php'));
    }

    /**
     * Permission structure
     * @return array
     */
    public static function getPermissionStructure()
    {
        return [
            [
                [
                    Yii::t('app', 'Client Admin'),
                    'client.backend.admin',
                ],
                [
                    Yii::t('app', 'Client'),
                    'client.backend.default',
                ],
                [
                    Yii::t('app', 'Counterparty'),
                    'client.backend.counterparty',
                ],
            ],
        ];
    }

}
