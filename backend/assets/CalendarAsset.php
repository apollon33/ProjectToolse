<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets;

use yii\web\AssetBundle;

use yii\web\View;


/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CalendarAsset extends AssetBundle
{

    public function init()
    {
        $this->jsOptions['position'] = View::POS_END;
        parent::init();
    }


    public $js = [
        'js/CalendarHelper.js',
        'js/CalendarVacationHelper.js',
    ];

    public $depends = [
        'backend\assets\AppAsset',
    ];
}
