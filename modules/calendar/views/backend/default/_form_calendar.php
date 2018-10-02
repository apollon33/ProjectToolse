<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;
use common\models\User;
use modules\calendar\models\Calendar;
use modules\project\models\Project;
use kartik\datetime\DateTimePicker;
use common\components\DurationConverter;

use common\access\AccessManager;

$userAgeLimit  = 100;
$datePickerRange = (date('Y') - $userAgeLimit) . ':' . date('Y');
$created_by=Yii::$app->user->identity->id;
?>

<?php $form = ActiveForm::begin([
    'action' => (isset($path) ? Url::to([$path]): ''),
    'options' => [
        'enctype' => 'multipart/form-data',
        'class' => 'form-horizontal',
        'id' => 'calendarCreate',
        'data' => ['pjax' => true] ]]); ?>

<div class="panel panel-default">

    <div class="panel-body">

<?= $form->field($calendar, 'id', ['options' => ['class' => 'hidden']])->textInput(['value'=>$calendar->id])?>

<?= $form->field($calendar, 'user_id')->dropDownList(User::getListDaveloper(),[
    'prompt' => 'Select...'
]);?>

<div class="form-group pull-right">
    <div class="col-md-12 col-sm-12">
        <?= Html::a('Assign to me', '#', ['id' => 'assign-to-me', 'data-user-id' => Yii::$app->user->identity->id]) ?>
    </div>
</div>
<div class="clearfix"></div>

<?= $form->field($calendar, 'project_id')->dropDownList([
    'prompt' => 'Select...'
]);?>

<div class="form-group hidden" id="no-projects-assigned">
    <div class="col-md-10 col-sm-10 col-sm-offset-2 col-md-offset-2 col-xs-offset-12">
        <?= Yii::t('app', 'No projects are assigned to the selected user. You can assign the user in the {link}.', ['link' => Html::a(Yii::t('app', 'project list'), ['/project'], ['target' => '_blank'])]) ?>
    </div>
</div>

<?php if (empty($calendar->estimated_time)): ?>
    <div class="form-group pull-right">
        <div class="col-md-12 col-sm-12">
            <?= Html::a('Estimate', '#', ['id' => 'show-estimate']) ?>
        </div>
    </div>
    <div class="clearfix"></div>
<?php endif; ?>

<div id="estimate-block" <?php if (empty($calendar->estimated_time)): ?> class="hidden" <?php endif; ?>>
    <?= $form->field($calendar, 'estimated_time')->textInput([
            'value'=>(!$calendar->isNewRecord  ?  DurationConverter::shortDuration(Yii::$app->formatter->asDuration($calendar->estimated_time)):'' ),
    ])->label(Yii::t('app', 'Estimated time (eg. 3w 4d 12h 30m)')); ?>
    <?= $form->field($calendar, 'estimate_approval', ['inputOptions' => ['class' => 'form-control', 'disabled' => !Yii::$app->user->identity->isAllowedToViewStage()]])->dropdownList(Calendar::getApprovals()); ?>
</div>
<div class="clearfix"></div>

<?=  $form->field($calendar, 'start_at')->widget(DateTimePicker::className(),[
    'options' => [
        'placeholder' => 'Start time...',
        'value'=>(!empty($calendar->start_at)  ?  date('Y-m-d H:i',$calendar->start_at): '' ),
    ],
    'pluginOptions' => ['autoclose' => true],

]); ?>

<?=  $form->field($calendar, 'end_at')->widget(DateTimePicker::className(),[
    'options' => [
        'placeholder' => 'Start time...',
        'value'=>(!empty($calendar->end_at) ?  date('Y-m-d H:i',$calendar->end_at):'' ),
    ],
    'pluginOptions' => ['autoclose' => true],

]); ?>

<?= $form->field($calendar,'actual_time')->textInput();?>

<?=
$form->field($calendar, 'created_by')->dropDownList(User::getList(), [
    'value' => $created_by,
    'disabled' => (Yii::$app->user->can(AccessManager::UPDATE) ?
        false
        : true),
])

?>

<?= $form->field($calendar, 'created_by', ['options' => ['class' => 'hidden']])->textInput(['value'=>$created_by,'id'=>'created-by_calendar'])?>

<?=  (!$calendar->isNewRecord  ?
$form->field($calendar, 'created_at', ['options' => ['class' => 'hidden']])->textInput(['value' => !empty($calendar->created_at) ? date('Y-m-d', $calendar->created_at) : null])
    .$form->field($calendar, 'created_at')->widget(DatePicker::classname(), [
        'clientOptions' => ['changeMonth' => true, 'changeYear' => true, 'yearRange' => $datePickerRange, 'altFormat' => 'yy-mm-dd', 'altField' => '#calendar-created_at'],
        'options' => ['class' => 'form-control', 'readonly' => true, 'id' => 'date-pickercreated_at', 'name' => 'date-pickercreated_at','disabled'=>true,'style'=>'background-color:#eee']
    ])

    .$form->field($calendar, 'updated_at', ['options' => ['class' => 'hidden']])->textInput(['value' => !empty($calendar->updated_at) ? date('Y-m-d', $calendar->updated_at) : null])
    .$form->field($calendar, 'updated_at')->widget(DatePicker::classname(), [
        'clientOptions' => ['changeMonth' => true, 'changeYear' => true, 'yearRange' => $datePickerRange, 'altFormat' => 'yy-mm-dd', 'altField' => '#calendar-updated_at'],
        'options' => ['class' => 'form-control', 'readonly' => true, 'id' => 'date-pickerupdate_at', 'name' => 'date-pickerupdate_at','disabled'=>true,'style'=>'background-color:#eee']
    ]): '')?>

<?= $form->field($calendar, 'description')->textarea(['rows' => 6]) ?>
    </div>
</div>

<div class="pull-right">

    <?= Html::a(Yii::t('app', 'Cancel'),'index', ['class' => 'btn btn-primary cancel-modal']) ?>
    <?= Html::submitButton($calendar->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-success']) ?>
    <?= (isset($path) ? ($calendar->isNewRecord ? '':Html::a( Yii::t('app', 'Delete'), ['delete', 'id' => $calendar->id],  ['class' => 'btn btn-danger pjax-modal-campaign','id'=>'removeCalendarDate'])) : '') ?>

</div>
<?php ActiveForm::end(); ?>

<div class="clearfix"></div>