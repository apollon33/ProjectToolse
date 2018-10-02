<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\MultipleEMails;

/* @var $this yii\web\View */
/* @var $model common\models\MultipleEMails */
/* @var $form yii\widgets\ActiveForm */
?>


    <?php $form = ActiveForm::begin([
        'options' => [
            'action' =>  Url::to([$path]),
            'class' => 'form-horizontal',
            'enctype' => 'multipart/form-data',
            'id' => 'emailCreate',
            'data' => ['pjax' => true] ]]); ?>

    <div class="panel panel-default">

        <div class="panel-body">

            <?= $form->field($model, 'userId', ['options' => ['class' => 'hidden']])->textInput(['value'=>(!empty($userId)? $userId : $model->id)])?>

            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'type')->dropDownList(MultipleEMails::getTypeList()) ?>

            <?= $form->field($model, 'active')->checkbox() ?>

        </div>

    </div>

    <div class="pull-right">
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-primary cancel-modal']) ?>
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="clearfix"></div>


