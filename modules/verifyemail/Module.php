<?php

namespace modules\verifyemail;

use Yii;
use yii\base\Module as BaseModule;
use common\library\permission\ModulePermission;

/**
 * user module definition class
 */
class Module extends BaseModule
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'modules\verifyemail\controllers';

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
                    Yii::t('app', 'Verify Email'),
                    'verifyemail.backend.default',
                ],
            ],
        ];
    }
}
