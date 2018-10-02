<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\jui\DatePicker;
use modules\position\models\Position;

/* @var $this yii\web\View */
/* @var $model modules\user\models\User */
/* @var $form yii\widgets\ActiveForm */

$userAgeLimit  = 100;
$datePickerRange = (date('Y') - $userAgeLimit) . ':' . date('Y');
?>
<div id="result-ajax">

    <div class="user-form col-lg-12 alert alert-info">

        <?php $form = ActiveForm::begin(['action' =>  Url::to(['create']),'options' => ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal','id' => 'positionCreate','data' => ['pjax' => true]]]); ?>

        <?= $form->field($model, 'user_id', ['options' => ['class' => 'hidden']])->textInput(['value'=>(!empty($user_id)? $user_id : '')])?>

        <?= $form->field($model, 'created_at', ['options' => ['class' => 'hidden']])->textInput(['value' => $model->created_at=date('Y-m-d')]) ?>

        <?= $form->field($model, 'created_at')->widget(DatePicker::classname(), [
            'clientOptions' => ['changeMonth' => true, 'changeYear' => true, 'yearRange' => $datePickerRange, 'altFormat' => 'yy-mm-dd', 'altField' => '#logposition-created_at'],
            'options' => ['class' => 'form-control', 'readonly' => true, 'id' => 'date-picker', 'name' => 'date-picker']
        ]) ?>

        <?= $form->field($model, 'position_id')->dropDownList(Position::getList(),['prompt' => 'Select...'])?>

        <?= $form->field($model, 'description')->textarea(['rows' => 6,'value'=>""]) ?>



        <div class="pull-right">

            <?= Html::a(Yii::t('app', 'Cancel'),'index', ['class' => 'btn btn-primary cancel-modal']) ?>

            <?=  Html::submitButton(!$model->isNewRecord ?  Yii::t('app', 'Save') : Yii::t('app', 'Create') , ['class' =>(!$model->isNewRecord ? 'btn btn-success':'btn btn-success positionSave'),])?>
        </div>



        <?php ActiveForm::end(); ?>

        <div class="clearfix"></div>

    </div>
</div>



