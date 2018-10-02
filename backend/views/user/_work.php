<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Tabs;
use yii\jui\DatePicker;
use common\models\User;
use kartik\file\FileInput;
use modules\country\models\Country;
use modules\logposition\models\LogPosition;
use modules\position\models\Position;
use modules\logsalary\models\LogSalary;
use modules\registration\models\Registration;
use modules\logregistration\models\LogRegistration;

use common\access\AccessManager;

/* @var yii\web\View $this */
/* @var common\models\User $model */
/* @var yii\widgets\ActiveForm $form */

$session = Yii::$app->session;
$filterDate = $session->get('filterDate');
$userAgeLimit = 100;
$datePickerRange = (date('Y') - $userAgeLimit) . ':' . date('Y');
?>
<div class="form-group">
    <div class="col-sm-12">
        <?= (Yii::$app->user->can('core.backend.admin:' . AccessManager::UPDATE) ? $form->field($model,
            'verified')->dropdownList(User::getVerifyStatuses()) : '') ?>
    </div>
    <div class="col-sm-12">
        <?= $form->field($model, 'resume', [
            'template' => '{label}<div class="col-sm-10">{input}{error}{hint}</div>',
            'labelOptions' => ['class' => 'control-label  col-sm-2 '],
        ])->widget(FileInput::classname(), [
            'pluginOptions' => [
                'showPreview' => false,
                'showCaption' => true,
                'showRemove' => true,
                'showUpload' => false,
            ],
            'options' => [
                'disabled' => Yii::$app->user->can('core.backend.admin:' . AccessManager::UPDATE) ? false : true,
            ],
        ]) ?>
    </div>
    <?= (!empty($model->resume) ?
        '<div class="col-sm-2">' . Html::a(Yii::t('app', 'Resume'), ['file', 'id' => $model->id, 'name' => 'resume'],
            ['class' => 'btn btn-primary']) . '</div>' : '') ?>
    <?= $form->field($model, 'resume',['options' => ['class' => 'hidden']])->textInput()?>
    <div class="col-sm-12">
        <?= $form->field($model, 'interview', ['options' => ['class' => 'hidden']])->textInput([
            'value' => !empty($model->interview) ? date('Y-m-d', $model->interview) : null,
        ]) ?>
        <?= $form->field($model, 'interview')->widget(DatePicker::classname(), [
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => $datePickerRange,
                'altFormat' => 'yy-mm-dd',
                'altField' => '#user-interview',
            ],
            'options' => [
                'class' => 'form-control',
                'readonly' => true,
                'id' => 'date-pickerinterview',
                'name' => 'date-pickerinterview',
                'disabled' => Yii::$app->user->can('core.backend.admin:' . AccessManager::UPDATE) ? false : true
            ],
        ]) ?>

    </div>
    <div class="col-sm-12">
        <?= $form->field($model, 'note')->textarea(['rows' => 6, 'disabled' => Yii::$app->user->can('core.backend.admin:' . AccessManager::UPDATE) ? false : true]) ?>

    </div>
    <div class="col-sm-8 col-xs-6 col-md-9">
        <?= $form->field($model, 'position_id', [
            'template' => '{label}<div class="col-sm-10 input_work">{input}{error}{hint}</div>',
            'labelOptions' => ['class' => 'control-label  col-sm-2 label_work'],
        ])->dropDownList(Position::getList(),
            ['disabled' => true, 'value' => $model->position_id, 'prompt' => 'Select...',]) ?>

    </div>
    <div class="col-sm-4 col-md-3 col-xs-6">
        <?= Yii::$app->user->can('core.backend.admin:' . AccessManager::UPDATE) ? Html::a(Yii::t('app', 'Change'),
            (!$model->isNewRecord ? ['/logposition/update', 'id' => $model->id] : ['/logposition/create']), ['class' => 'btn btn-primary id_position buttom_btm pjax-modal-position']) : ''?>
        <?=(!$model->isNewRecord ? Html::a(Yii::t('app', 'Show log'), null, ['class' => 'btn btn-info', 'id' => 'positionSdow']) : '')?>

    </div>
    <div class="col-sm-12">
        <div id="show_position" class="alert alert-info" style="display: none">
            <?= (!$model->isNewRecord ? $this->render("_form_show_position", [
                'dataProvider' => $dataProviderPosition,
            ]) : '') ?>


        </div>
    </div>
    <div class="col-sm-8 col-xs-6 col-md-9">
        <?= $form->field($model, 'registration_id', [
            'template' => '{label}<div class="col-sm-10 input_work">{input}{error}{hint}</div>',
            'labelOptions' => ['class' => 'control-label  col-sm-2 label_work'],
        ])->dropDownList(Registration::getList(),
            ['disabled' => true, 'value' => $model->registration_id, 'prompt' => 'Select...']) ?>

    </div>
    <div class="col-sm-4 col-md-3 col-xs-6">
        <?= Yii::$app->user->can('core.backend.admin:' . AccessManager::UPDATE) ? Html::a(Yii::t('app', 'Change'),
        (!$model->isNewRecord
            ? ['/logregistration/update', 'id' => $model->id]
            : ['/logregistration/create']), ['class' => 'btn btn-primary registration buttom_btm pjax-modal-registration']) : ''?>
        <?= (!$model->isNewRecord
            ? Html::a(Yii::t('app', 'Show log'), null, ['class' => 'btn btn-info', 'id' => 'registrationSdow'])
            : '') ?>

    </div>
    <div class="col-sm-12">
        <div id="show_registration" class="alert alert-info" style="display: none">
            <?= (!$model->isNewRecord ? $this->render("_form_show_registration", [
                'dataProviderRegistration' => $dataProviderRegistration,
            ]) : '') ?>


        </div>
    </div>
    <div class="col-sm-4 col-md-5 col-xs-4">
        <?= $form->field($model, 'salary', [
            'template' => '{label}<div class="col-sm-7">{input}{error}{hint}</div>',
            'labelOptions' => ['class' => 'control-label  col-sm-5 label_salary'],
        ])->textInput(['disabled' => true]) ?>


    </div>
    <div class="col-sm-4 col-md-4 col-xs-4">
        <?= $form->field($model, 'reporting_salary', [
            'template' => '{label}<div class="col-sm-7">{input}{error}{hint}</div>',
            'labelOptions' => [
                'class' => 'control-label  col-sm-5 ',
                'style' => ['width' => '40%'],
            ],
        ])->textInput(['disabled' => true, 'value' => $model->reporting_salary . ' ₴ UAH']) ?>


    </div>
    <div class="col-sm-4 col-md-3 col-xs-4">
        <?= Yii::$app->user->can('core.backend.admin:' . AccessManager::UPDATE) ? Html::a(Yii::t('app', 'Change'),
            (!$model->isNewRecord ? ['/logsalary/update', 'id' => $model->id] : ['/logsalary/create']),
            ['class' => 'btn btn-primary salary buttom_btm pjax-modal-salary']) : ''?>
        <?=(!$model->isNewRecord ? Html::a(Yii::t('app', 'Show log'), null,
            ['class' => 'btn btn-info', 'id' => 'salarySdow']) : '')?>

    </div>
    <div class="col-sm-12">
        <div id="show_salary" class="alert alert-info" style="display: none">
            <?= (!$model->isNewRecord ? $this->render("_form_show_salary", [
                'dataProviderSalary' => $dataProviderSalary,
            ]) : '') ?>


        </div>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'date_receipt', ['options' => ['class' => 'hidden']])->textInput([
            'value' => !empty($model->date_receipt) ? date('Y-m-d', $model->date_receipt) : null,
        ]) ?>
        <?= $form->field($model, 'date_receipt', [
            'template' => '{label}<div class="col-sm-8">{input}{error}{hint}</div>',
            'labelOptions' => ['class' => 'control-label  col-sm-4 '],
        ])->widget(DatePicker::classname(), [
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => $datePickerRange,
                'altFormat' => 'yy-mm-dd',
                'altField' => '#user-date_receipt',
            ],
            'options' => [
                'class' => 'form-control',
                'readonly' => true,
                'id' => 'date-pickerdate_receipt',
                'name' => 'date-pickerdate_receipt',
                'disabled' => Yii::$app->user->can('core.backend.admin:' . AccessManager::UPDATE) ? false : true
            ],
        ]) ?>

    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'date_dismissal', ['options' => ['class' => 'hidden']])->textInput([
            'value' => !empty($model->date_dismissal) ? date('Y-m-d', $model->date_dismissal) : null,
        ]) ?>
        <?= $form->field($model, 'date_dismissal', [
            'template' => '{label}<div class="col-sm-8">{input}{error}{hint}</div>',
            'labelOptions' => ['class' => 'control-label  col-sm-4 '],
        ])->widget(DatePicker::classname(), [
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => $datePickerRange,
                'altFormat' => 'yy-mm-dd',
                'altField' => '#user-date_dismissal',
            ],
            'options' => [
                'class' => 'form-control',
                'readonly' => true,
                'id' => 'date-pickerdate_dismissal',
                'name' => 'date-pickerdate_dismissal',
                'disabled' => Yii::$app->user->can('core.backend.admin:' . AccessManager::UPDATE) ? false : true
            ],
        ]) ?>
    </div>
</div>
<?= Yii::$app->user->can('core.backend.admin:' . AccessManager::UPDATE) ? $form->field($model, 'active')->dropdownList(User::getActiveStatuses()) : ''?>
