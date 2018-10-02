<?php

use yii\helpers\Html;
use modules\payment\models\Payment;

/* @var $this yii\web\View */
/* @var $model modules\payment\models\Payment */

$this->title = Yii::t('app', 'Add Task');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Task'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calendar-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="profile-form col-lg-8 alert alert-info">
    <?=  $this->render('_form_calendar', [
        'calendar' => $calendar,
    ])?>
    </div>


</div>
