<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use modules\holiday\models\Holiday;

/* @var $this yii\web\View */
/* @var $model modules\holiday\models\Holiday */
/* @var $form yii\widgets\ActiveForm */

$userAgeLimit  = 100;
$datePickerRange = (date('Y') - $userAgeLimit) . ':' . date('Y');
?>

<div class="holiday-form col-lg-8 alert alert-info">

    <?php $form = ActiveForm::begin(); ?>

    <div class="panel panel-default">

        <div class="panel-heading"><b><?= Yii::t('app', 'Holiday') ?></b></div>

        <div class="panel-body">



            <?= $form->field($model, 'date', ['options' => ['class' => 'hidden']])->textInput(['value' => !empty($model->date) ? date('Y-m-d', $model->date) : null]) ?>

            <?= $form->field($model, 'date')->widget(DatePicker::classname(), [
                'clientOptions' => ['changeMonth' => true, 'changeYear' => true, 'yearRange' => $datePickerRange, 'altFormat' => 'yy-mm-dd', 'altField' => '#holiday-date'],
                'options' => ['class' => 'form-control', 'readonly' => true, 'id' => 'date-picker', 'name' => 'date-picker']
            ]) ?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'type')->dropDownList(Holiday::getTypeHoliday())?>


        </div>

    </div>

    <div class="pull-right">
        <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="clearfix"></div>

</div>