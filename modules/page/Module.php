<?php

namespace modules\page;

use common\library\permission\ModulePermission;
use Yii;
use yii\base\Module as BaseModule;

/**
 * page module definition class
 */
class Module extends BaseModule
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'modules\page\controllers';

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
     *
     * Accessible formats of permission tree:
     *
     * module.action
     * module.side.action
     * module.side.controller.action
     *
     * Parental permission must be specified first.
     *
     * @return array
     */
    public static function getPermissionStructure()
    {
        return [
            [
                [
                    Yii::t('app', 'Page'),
                    'page.backend.default',
                ],
                [
                    Yii::t('app', 'Page'),
                    'page.backend.category',
                ],
            ],
        ];
    }
}
