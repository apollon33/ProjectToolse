<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\jui\DatePicker;
use modules\registration\models\Registration;

/* @var $this yii\web\View */
/* @var $model modules\user\models\LogRegistration */
/* @var $form yii\widgets\ActiveForm */

$userAgeLimit  = 100;
$datePickerRange = (date('Y') - $userAgeLimit) . ':' . date('Y');
?>

<div id="result-ajax">
    <div class="user-form col-lg-12 alert alert-info">

        <?php $form = ActiveForm::begin(['action' =>  Url::to(['create']),'options' => ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal','id' => 'registrationCreate','data' => ['pjax' => true]]]); ?>

        <?= $form->field($model, 'user_id', ['options' => ['class' => 'hidden']])->textInput(['value'=>(!empty($user_id)? $user_id:'')])?>

        <?= $form->field($model, 'created_at', ['options' => ['class' => 'hidden']])->textInput(['value' => $model->created_at=date('Y-m-d')]) ?>

        <?= $form->field($model, 'created_at')->widget(DatePicker::classname(), [
            'clientOptions' => ['changeMonth' => true, 'changeYear' => true, 'yearRange' => $datePickerRange, 'altFormat' => 'yy-mm-dd', 'altField' => '#logregistration-created_at'],
            'options' => ['class' => 'form-control', 'readonly' => true, 'id' => 'date-picker1', 'name' => 'date-picker1']
        ]) ?>

        <?= $form->field($model, 'registration_id')->dropDownList(Registration::getList(),['prompt' => 'Select...'])?>

        <?= $form->field($model, 'description')->textarea(['rows' => 6,'value'=>'']) ?>

        <div class="pull-right">

            <?= Html::a(Yii::t('app', 'Cancel'),'index', ['class' => 'btn btn-primary cancel-modal']) ?>

            <?=  Html::submitButton(!$model->isNewRecord ?  Yii::t('app', 'Save') : Yii::t('app', 'Create') , ['class' =>(!$model->isNewRecord ? 'btn btn-success':'btn btn-success registrationSave'),])?>
        </div>

        <?php ActiveForm::end(); ?>
        <div class="clearfix"></div>
    </div>
</div>


