<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\project\models\Project */

$this->title = Yii::t('app', 'Edit Project') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Projects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="project-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="project-form col-lg-8 alert alert-info">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>

</div>
