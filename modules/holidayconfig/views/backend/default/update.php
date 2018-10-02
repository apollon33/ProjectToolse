<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\holidayconfig\models\HolidayConfig */

$this->title = Yii::t('app', 'Edit Item') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Holiday Configs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="holiday-config-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
