<?php

namespace modules\order;

use Yii;
use yii\base\Module as BaseModule;
use common\library\permission\ModulePermission;

/**
 * order module definition class
 */
class Module extends BaseModule
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'modules\order\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
        Yii::configure($this, require(__DIR__ . '/config/params.php'));
    }

    public static function getPermissionStructure()
    {
        return [
            [
                [
                    Yii::t('app', 'Order'),
                    'order.backend.default',
                ],
            ],
        ];
    }

}
