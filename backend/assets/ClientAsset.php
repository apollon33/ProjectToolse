<?php
namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Class ClientAsset
 * @package backend\assets
 */
class ClientAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/ClientHelper.js',
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}
