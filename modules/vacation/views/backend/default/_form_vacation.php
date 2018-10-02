<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\date\DatePicker;
use common\models\User;
use modules\vacation\models\Vacation;
use yii\helpers\ArrayHelper;
use kartik\datetime\DateTimePicker;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use backend\assets\VacationAssets;

VacationAssets::register($this);

$userAgeLimit = 100;
$datePickerRange = (date('Y') - $userAgeLimit) . ':' . date('Y');
?>


<div id="result-ajax">


    <div class="user-form col-lg-12 alert alert-info">
        <?php $form = ActiveForm::begin([
            'options' => [
                'enctype' => 'multipart/form-data',
                'class' => 'form-horizontal',
                'id' => 'vacationForm',
                'data-pjax' => true,
            ],
        ]); ?>

        <?= $form->field($vacation, 'id', ['options' => ['class' => 'hidden']])->textInput() ?>

        <?= $form->field($vacation, 'user_id',
            empty($userId) ? [] : ['options' => ['class' => 'hidden']])->dropDownList(User::getList(), [
            'prompt' => 'Select...',
            'value' => empty($userId) ? '' : $userId,
        ]); ?>
        <?= $form->field($vacation, 'type')->dropDownList(Vacation::getCurrency(), [
            'errorOptions' => ['class' => 'help-block', 'encode' => false],
            'prompt' => 'Select...',
        ]); ?>

        <?= $form->field($vacation, 'checkbox', [
            'options' => ['class' => 'hidden'],
            'template' => '<div class="col-sm-12">{input}{error}{hint}</div>',
        ])->checkbox() ?>

        <?= $form->field($vacation, 'approve',
            ['options' => ['class' => 'hidden']])->dropDownList(Vacation::getApproveStatuses()) ?>

        <?= $form->field($vacation, 'managerId')->dropDownList(User::getList(),
            ['prompt' => 'Select...']); ?>


        <?= $form->field($vacation, 'start_at', ['options' => ['class' => 'hidden']])->textInput() ?>

        <?= $form->field($vacation, 'start_at')->widget(DatePicker::classname(), [
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
                'value' => $vacation->start_at ? Yii::$app->formatter->asDate($vacation->start_at, 'php:Y-m-d') : '',
            ],
            'pluginOptions' => [
                'todayHighlight' => true,
                'daysOfWeekDisabled' => [0,6],
                'weekStart'=>1,
                'format' => 'yyyy-mm-dd',
                'showOnFocus' => true,
                'autoclose' => true
            ]
        ]) ?>

        <?= $form->field($vacation, 'end_at', ['options' => ['class' => 'hidden']])->textInput() ?>

        <?= $form->field($vacation, 'end_at')->widget(DatePicker::classname(), [
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
                'value' => $vacation->end_at ? Yii::$app->formatter->asDate($vacation->end_at, 'php:Y-m-d') : '',
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

        <?= $form->field($vacation, 'description')->textarea(['rows' => 6]) ?>

        <div class="pull-right">

            <?= Html::a(Yii::t('app', 'Cancel'), 'index', ['class' => 'btn btn-primary cancel-modal']) ?>
            <?= Html::submitButton($vacation->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
                ['class' => 'btn btn-success']) ?>
            <?= ($vacation->isNewRecord ? '' : Html::a(Yii::t('app', 'Delete'),
                ['delete-user-vacation', 'id' => $vacation->id],
                ['class' => 'btn btn-danger pjax-modal-vacation', 'id' => 'removeVacationDate'])) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <div class="clearfix"></div>
    </div>

</div>

