<?php
namespace modules\config;

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
    public $controllerNamespace = 'modules\config\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
        $properties = array_merge_recursive(
            require(__DIR__ . '/config/params.php'),
//            require(__DIR__ . '/../role/config/params.php'),
            require(__DIR__ . '/../holiday/config/params.php'),
            require(__DIR__ . '/../holidayconfig/config/params.php'),
            require(__DIR__ . '/../registration/config/params.php')
        );
        Yii::configure($this, $properties);
    }

    /**
     * Permission structure
     *
     * @return array
     */
    public static function getPermissionStructure()
    {
        return [
            [
                [
                    Yii::t('app', 'Config'),
                    'config',
                ],
            ],
        ];
    }
}

?>
