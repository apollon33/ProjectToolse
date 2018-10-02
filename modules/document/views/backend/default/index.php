<?php

use yii\helpers\Html;
use yii\helpers\Url;
use modules\document\models\TreeViewQMS;
use kartik\tree\Module;
use modules\document\models\Document;

/* @var $this yii\web\View */
/* @var $searchModel modules\document\models\DocumentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Documents');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= TreeViewQMS::widget([
        'query' => Document::queryWithPermissions(),
        'displayValue' => $id,

        'nodeActions' => [
            Module::NODE_MANAGE => Url::to(['/document/document-preview']),
            Module::NODE_REMOVE => Url::to(['/document/document-remove']),
            Module::NODE_MOVE => Url::to(['/document/document-move']),
        ],
        'nodeView' => '@modules/document/views/backend/default/_form',
        'nodeFormOptions' => ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data'],
        'headingOptions' => ['label' => Yii::t('app', 'Categories')],
        'emptyNodeMsg' => Yii::t('app', 'Select a tree node to display.'),
        'treeOptions' => ['style' => 'height: 650px'],
        'buttonOptions' => ['class' => 'btn btn-success'],
        'rootOptions' => ['class' => 'text-primary', 'label' => Yii::t('app', 'Documents')],
        'treeWrapperOptions' => ['class' => 'kv-tree-wrapper form-control'],
        'detailOptions' => ['style' => 'overflow: visible'],
    ]); ?>

    <br/>

</div>