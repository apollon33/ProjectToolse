<?php

namespace modules\logregistration;

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
    public $controllerNamespace = 'modules\logregistration\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

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
                    Yii::t('app', 'Log Registration'),
                    'logregistration.backend.default',
                ],
            ],
        ];
    }
}
