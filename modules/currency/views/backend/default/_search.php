<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\currency\models\CurrencySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="currency-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'key_digital') ?>

    <?= $form->field($model, 'key_letter') ?>

    <?= $form->field($model, 'date') ?>

    <?= $form->field($model, 'name') ?>

    <?php // echo $form->field($model, 'rate') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
