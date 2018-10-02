<?php

namespace modules\position;


use Yii;
use yii\base\Module as BaseModule;
use common\library\permission\ModulePermission;

/**
 * setting module definition class
 */
class Module extends BaseModule
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'modules\position\controllers';

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
                    Yii::t('app', 'Position'),
                    'position.backend.default',
                ],
            ],
        ];
    }
}
