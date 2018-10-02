<?php

namespace modules\catalog;

use Yii;
use yii\base\Module as BaseModule;
use common\library\permission\ModulePermission;

/**
 * catalog module definition class
 */
class Module extends BaseModule
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'modules\catalog\controllers';

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
                    Yii::t('app', 'Catalog'),
                    'catalog.backend.category',
                ],
                [
                    Yii::t('app', 'Catalog'),
                    'catalog.backend.product'
                ],
            ],
        ];
    }

}
