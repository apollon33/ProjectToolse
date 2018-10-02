<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use modules\vacation\models\Vacation;
use yii\helpers\Html;
use common\models\User;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model modules\vacation\models\Vacation */
/* @var $form yii\widgets\ActiveForm */
$session = Yii::$app->session;
$filterDate = $session->get('filterDate');
$userAgeLimit  = 100;
$datePickerRange = (date('Y') - $userAgeLimit) . ':' . (date('Y')+5);

$optionFiled = [
    'template' => '{label}<div class="col-sm-10">{input}{error}{hint}</div>',
    'labelOptions' => ['class' => 'control-label  col-sm-2 '],
];
?>

<div class="vacation-form col-lg-8 alert alert-info">

    <?php $form = ActiveForm::begin([
            'options' => [
                'class' => 'form-horizontal',
            ],
        ]); ?>

    <div class="panel panel-default">

        <div class="panel-heading"><b><?= Yii::t('app', 'Vacation') ?></b></div>

        <div class="panel-body">


            <?= $form->field($model, 'user_id', $optionFiled)->dropDownList(User::getList(),['prompt' => 'Select...']);?>

            <?= $form->field($model, 'type', $optionFiled)->dropDownList(Vacation::getCurrency(),[
                'errorOptions' => ['class' => 'help-block' ,'encode' => false],
                'prompt' => 'Select...'
            ]);?>
            <?php if(\Yii::$app->user->getIdentity()->isRole(User::ROLE_SUPER_ADMIN)): ?>
             <?= $form->field($model, 'checkbox', ['options' => ['class' => 'hidden']])->checkbox() ?>
            <?php endif;?>

            <?= $form->field($model, 'start_at', ['options' => ['class' => 'hidden']])->textInput() ?>

            <?= $form->field($model, 'start_at', $optionFiled)->widget(DatePicker::classname(), [
                'removeButton' => false,
                'pickerButton' => false,
                'type' => DatePicker::TYPE_INPUT,
                'pluginEvents' => [
                    "changeDate" => "function(e) {
                    var val = $('#date-picker').val(); 
                    $('#vacation-start_at').val(val);   }",
                ],
                'name' => 'date-picker',
                'options' => [
                    'id' => 'date-picker',
                    'value' => $model->start_at ? Yii::$app->formatter->asDate($model->start_at, 'php:Y-m-d') : '',
                ],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'daysOfWeekDisabled' => [0,6],
                    'weekStart'=>1,
                    'format' => 'yyyy-mm-dd',
                    'showOnFocus' => true,
                    'autoclose' => true,
                ]
            ]) ?>

            <?= $form->field($model, 'end_at', ['options' => ['class' => 'hidden']])->textInput() ?>

            <?= $form->field($model, 'end_at', $optionFiled)->widget(DatePicker::classname(), [
                'removeButton' => false,
                'pickerButton' => false,
                'type' => DatePicker::TYPE_INPUT,
                'pluginEvents' => [
                    "changeDate" => "function(e) {
                    var val = $('#date-picker1').val(); 
                    $('#vacation-end_at').val(val);   }",
                ],
                'name' => 'date-picker1',
                'options' => [
                    'id' => 'date-picker1',
                    'value' => $model->end_at ? Yii::$app->formatter->asDate($model->end_at, 'php:Y-m-d') : '',
                ],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'daysOfWeekDisabled' => [0,6],
                    'weekStart'=>1,
                    'format' => 'yyyy-mm-dd',
                    'showOnFocus' => true,
                    'autoclose' => true,
                ]
            ]) ?>

            <?= $form->field($model, 'approve', $optionFiled)->dropDownList(Vacation::getApproveStatuses()) ?>

            <?= $form->field($model, 'managerId', $optionFiled)->dropDownList(User::getList(),['prompt' => 'Select...']);?>

            <?= $form->field($model, 'description', $optionFiled)->textarea(['rows' => 6]) ?>

        </div>

    </div>

    <div class="pull-right">
        <?= Html::a(Yii::t('app', 'Cancel'), ['table'], ['class' => 'btn btn-primary']) ?>
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="clearfix"></div>

</div>