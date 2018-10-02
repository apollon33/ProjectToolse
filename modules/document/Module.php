<?php

namespace modules\document;

use Yii;
use yii\base\Module as BaseModule;
use common\library\permission\ModulePermission;

/**
 * document module definition class
 */
class Module extends BaseModule
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'modules\document\controllers';

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
                    Yii::t('app', 'Document'),
                    'document.backend.default',
                ],
            ],
        ];
    }
}
