<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\vacation\models\Vacation */

$this->title = Yii::t('app', 'Edit Vacation') . ': ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Vacations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="vacation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
