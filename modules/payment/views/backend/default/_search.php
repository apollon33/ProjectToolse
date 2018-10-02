<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\payment\models\PaymentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'date') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'tax_profit') ?>

    <?php // echo $form->field($model, 'tax_war') ?>

    <?php // echo $form->field($model, 'tax_pension') ?>

    <?php // echo $form->field($model, 'payout') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
