<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\holiday\models\Holiday */

$this->title = Yii::t('app', 'Edit Task') . ': ' . $calendar->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Task'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $calendar->id, 'url' => ['view', 'id' => $calendar->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="calendar-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="profile-form col-lg-8 alert alert-info">
        <?=  $this->render('_form_calendar', [
            'calendar' => $calendar,
        ]) ?>
    </div>



</div>
