<?php

namespace modules\i18n;

use Yii;
use yii\base\Module as BaseModule;
use common\library\permission\ModulePermission;

/**
 * translation module definition class
 */
class Module extends BaseModule
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'modules\translation\controllers';

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
                    Yii::t('app', 'i18n'),
                    'i18n.backend.default',
                ],
                [
                    Yii::t('app', 'i18n'),
                    'i18n.backend.language',
                ],
            ],
        ];
    }

}
