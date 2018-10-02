<?php

namespace backend\assets;

use kartik\base\AssetBundle;

class TreeViewAssetDocument extends AssetBundle
{
    public $baseUrl = '@web';

    public $js = [
        'js/KvTreeHelper.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'backend\assets\TreeViewAssetQMS'
    ];
}
