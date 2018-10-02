<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model modules\currency\models\Currency */
/* @var $form yii\widgets\ActiveForm */
$userAgeLimit  = 100;
$datePickerRange = (date('Y') - $userAgeLimit) . ':' . date('Y');
?>


<div class="currency-form col-lg-8 alert alert-info">

    <?php $form = ActiveForm::begin(); ?>

    <div class="panel panel-default">

        <div class="panel-heading"><b><?= Yii::t('app', 'Currency') ?></b></div>

        <div class="panel-body">

            <?= $form->field($model, 'key_digital')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'key_letter')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'date', ['options' => ['class' => 'hidden']])->textInput(['value' => !empty($model->date) ? date('Y-m-d', $model->date) : null])?>

            <?= $form->field($model, 'date')->widget(DatePicker::classname(), [
                'clientOptions' => ['changeMonth' => true, 'changeYear' => true, 'yearRange' => $datePickerRange, 'altFormat' => 'yy-mm-dd', 'altField' => '#currency-date'],
                'options' => ['class' => 'form-control', 'readonly' => true, 'id' => 'date-picker-date', 'name' => 'date-picker-date']
            ])?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'rate')->textInput() ?>

        </div>

    </div>

    <div class="pull-right">
        <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="clearfix"></div>

</div>