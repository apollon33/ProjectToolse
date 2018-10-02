<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\money\MaskMoney;
use modules\project\models\Project;
use kartik\select2\Select2;
use common\models\User;
use kartik\color\ColorInput;
use yii\jui\DatePicker;
use modules\client\models\Client;
use modules\profile\models\Profile;

/* @var $this yii\web\View */
/* @var $model modules\project\models\Project */
/* @var $form yii\widgets\ActiveForm */

$session = Yii::$app->session;
$filterDate = $session->get('filterDate');
$userAgeLimit  = 100;
$datePickerRange = (date('Y') - $userAgeLimit) . ':' . (date('Y')+5);
?>


    <?php $form = ActiveForm::begin([
        //'action' => (isset($path) ? Url::to([$path]): ''),
        'options' => [
            'enctype' => 'multipart/form-data',
            'class' => 'form-horizontal',
            'id' => 'projectUpdateName',
            'data' => ['pjax' => true] ]]); ?>


    <div class="panel panel-default">

        <div class="panel-heading"><b><?= Yii::t('app', 'Project') ?></b></div>

        <div class="panel-body">

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'client_id')->dropDownList(Client::getListTrash(),['prompt' => 'Select...','value'=>($model->isNewRecord  ? $filterDate['client_id']: $model->client_id)]) ?>

            <?= $form->field($model, 'profile_id')->dropDownList(Profile::getList(),['prompt' => 'Select...','value'=>($model->isNewRecord  ? $filterDate['profile_id']: $model->profile_id)]) ?>

            <?= $form->field($model, 'access')->dropdownList(Project::getActiveStatuses())?>

            <?=  $form->field($model, 'users')->widget(Select2::classname(), [
                'data' => User::getListDaveloper(),
                'options' => ['placeholder' => 'Select a user ...', 'multiple' => true],
                'pluginOptions' => [
                'tags' => true,
                'tokenSeparators' => [',', ' '],
                'maximumInputLength' => 10,
            ],
            ])->label('User'); ?>


            <?= $form->field($model, 'start_at', ['options' => ['class' => 'hidden']])->textInput(['value' => !empty($model->start_at) ? date('Y-m-d', $model->start_at) : null]) ?>

            <div class="col-sm-6">

            <?= $form->field($model, 'start_at',[
                'template' => '{label}<div class="col-sm-8">{input}{error}{hint}</div>',
                'labelOptions' => ['class' => 'control-label  col-sm-4 '],
                'options' => ['class' => 'hidden']
            ])->widget(DatePicker::classname(), [
                'clientOptions' => ['changeMonth' => true, 'changeYear' => true, 'yearRange' => $datePickerRange, 'altFormat' => 'yy-mm-dd', 'altField' => '#project-start_at'],
                'options' => ['class' => 'form-control', 'readonly' => true, 'id' => 'date-picker', 'name' => 'date-picker']
            ]) ?>

            </div>



            <?= $form->field($model, 'end_at', ['options' => ['class' => 'hidden']])->textInput(['value' => !empty($model->end_at) ? date('Y-m-d', $model->end_at) : null]) ?>

            <div class="col-sm-6">
            <?= $form->field($model, 'end_at',[
                'template' => '{label}<div class="col-sm-8">{input}{error}{hint}</div>',
                'labelOptions' => ['class' => 'control-label  col-sm-4 '],
                'options' => ['class' => 'hidden']
            ])->widget(DatePicker::classname(), [
                'clientOptions' => ['changeMonth' => true, 'changeYear' => true, 'yearRange' => $datePickerRange, 'altFormat' => 'yy-mm-dd', 'altField' => '#project-end_at'],
                'options' => ['class' => 'form-control', 'readonly' => true, 'id' => 'date-picker1', 'name' => 'date-picker1']
            ]) ?>
            </div>

            <?= $form->field($model, 'rate')->widget(MaskMoney::classname(), ['pluginOptions' => ['prefix' => '$ ', 'allowNegative' => false]]) ?>

            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

            <?= $form->field($model, 'color')->widget(ColorInput::classname(), [
                'options' => ['placeholder' => 'Select color ...'],
            ]);
            ?>

        </div>

    </div>

    <div class="pull-right">
        <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-primary cancel-modal']) ?>
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="clearfix"></div>

</div>