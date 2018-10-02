<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\buildingprocess\models\BuildingProcess */

$this->title = 'Edit Building Process' . ': ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Building Processes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="building-process-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'dataProvider' => $dataProvider,
        'dataTypeField' => $dataTypeField
    ]) ?>

</div>
