<?php

use yii\helpers\Html;
use modules\buildingprocess\models\document\DocumentViewQMS;
use modules\document\models\Document;

?>
<div class="document-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DocumentViewQMS::widget([
        'query' => Document::queryWithPermissions(),
        'nodeView' => '@modules/document/views/backend/default/_form',
        'nodeFormOptions' => ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data'],
        'headingOptions' => ['label' => Yii::t('app', 'Categories')],
        'emptyNodeMsg' => Yii::t('app', 'Select a tree node to display.'),
        'treeOptions' => ['style' => 'height: 650px'],
        'buttonOptions' => ['class' => 'btn btn-success'],
        'rootOptions' => ['class' => 'text-primary', 'label' => Yii::t('app', 'Documents')],
        'treeWrapperOptions' => ['class' => 'kv-tree-wrapper form-control'],
    ]); ?>

    <br/>

</div>