<?php

namespace backend\assets;

use kartik\base\AssetBundle;

class TreeViewAssetQMS extends AssetBundle
{
    public $sourcePath = '@vendor/kartik-v/yii2-tree-manager/assets';

    public $css = [
        'css/kv-tree.css',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\widgets\ActiveFormAsset',
        'yii\validators\ValidationAsset',
    ];
}
