<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use modules\country\models\Country;
use modules\currency\models\Currency;

/* @var $this yii\web\View */
/* @var $model modules\client\models\Company */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-form col-lg-8 alert alert-info">

    <?php $form = ActiveForm::begin(); ?>

    <div class="panel panel-default">

        <div class="panel-heading"><b><?= Yii::t('app', 'Counterparty') ?></b></div>

        <div class="panel-body">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'registration_number')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'vat')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'timezone')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'country')->dropDownList(Country::getList(),['prompt' => 'Select...']) ?>
            <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'payment_method')->dropDownList(['0' => Yii::t('app', 'Bank transfer'), '1' => Yii::t('app', 'PayPal'), '2' => Yii::t('app', 'Transferwise'), '3'  => Yii::t('app', 'sofort Überweisung'), '4' => Yii::t('app', 'Other')]) ?>
            <?= $form->field($model, 'currency')->dropDownList(Currency::getList(), ['prompt' => 'Select...']) ?>
            <?= $form->field($model, 'bank_name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'iban')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'swift')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'comments')->textarea() ?>
        </div>

    </div>

    <div class="pull-right">
        <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="clearfix"></div>

</div>