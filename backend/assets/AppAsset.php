<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'fonts/fonts.css',
        'css/flags.css',
        'css/site.css',
        'css/admin.css',
        'css/design.css',
        'css/main.css',
    ];
    public $js = [
        'library/clipboard.min.js',
        'js/SiteHelper.js',
        'js/DesignHelper.js',
        'js/RoleHelper.js',
        'js/DocumentHelper.js',
        'js/BuildingProcessHelper.js',
        'js/ImageHelper.js',
        'js/TableSortHelper.js',
        'js/PaymentHelper.js',
        'js/UserHelper.js',
        'js/ActionCalendarHelper.js',
        'js/ReportCartHelper.js',
        'js/ActivityHelper.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\jui\JuiAsset',
    ];
}
