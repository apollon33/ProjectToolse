<?php

namespace backend\assets;

use yii\web\AssetBundle;

use yii\web\View;


/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class VacationAssets extends AssetBundle
{
    public $css = [
        'library/handsontable/dist/handsontable.full.min.css'
    ];

    public $js = [
        'js/VacationHelper.js',
        'library/handsontable/dist/handsontable.full.min.js',
        'js/HandsontableHelper.js',

    ];

    public $depends = [
        'backend\assets\AppAsset',
    ];

    public function init()
    {
        parent::init();
        $this->jsOptions['position'] = View::POS_END;
    }
}
