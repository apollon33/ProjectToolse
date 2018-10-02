<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\holidayconfig\models\HolidayConfig */

$this->title = Yii::t('app', 'Add Item');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Holiday Configs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="holiday-config-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
