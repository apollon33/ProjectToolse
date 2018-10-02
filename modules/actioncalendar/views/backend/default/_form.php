<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use modules\holiday\models\Holiday;
use modules\actioncalendar\models\Event;

/* @var $this yii\web\View */
/* @var $model modules\actioncalendar\models\Event */
/* @var $form yii\widgets\ActiveForm */

$userAgeLimit  = 100;
$datePickerRange = (date('Y') - $userAgeLimit) . ':' . date('Y');

?>

<div class="event-form col-lg-8 alert alert-info">

    <?php $form = ActiveForm::begin(); ?>

    <div class="panel panel-default">

        <div class="panel-heading"><b><?= Yii::t('app', 'Event') ?></b></div>

        <div class="panel-body">

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'type')->dropDownList(Holiday::getTypeHoliday()) ?>

            <?= $form->field($model, 'location')->textarea(['rows' => 3]) ?>

            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

            <?=  $form->field($model, 'start_at')->widget(DateTimePicker::className(),[
                'options' => [
                    'placeholder' => $model->getAttributeLabel( 'start_at' ),
                    'value'=>(!empty($model->start_at)  ?  date('Y-m-d H:i',$model->start_at): '' ),
                ],
                'pluginOptions' => ['autoclose' => true],

            ]); ?>

            <?=  $form->field($model, 'end_at')->widget(DateTimePicker::className(),[
                'options' => [
                    'placeholder' => $model->getAttributeLabel( 'end_at' ),
                    'value'=>(!empty($model->end_at) ?  date('Y-m-d H:i',$model->end_at):'' ),
                ],
                'pluginOptions' => ['autoclose' => true],

            ]); ?>



            <?= $form->field($model, 'active')->dropDownList(Event::getActiveStatuses()) ?>

        </div>

    </div>

    <div class="pull-right">
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="clearfix"></div>

</div>