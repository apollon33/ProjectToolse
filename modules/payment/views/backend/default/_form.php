<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use common\models\User;
use modules\payment\models\Payment;
use modules\logsalary\models\LogSalary;
use common\helpers\Toolbar;


/* @var $this yii\web\View */
/* @var $model modules\payment\models\Payment */
/* @var $form yii\widgets\ActiveForm */
$session = Yii::$app->session;
$filterDate = $session->get('filterDate');
$userAgeLimit  = 100;
$datePickerRange = (date('Y') - $userAgeLimit) . ':' . (date('Y'));
?>

<div class="payment-form col-lg-8 alert alert-info">

    <?php $form = ActiveForm::begin(); ?>

    <div class="panel panel-default">

        <div class="panel-heading"><b><?= Yii::t('app', 'Payment') ?></b></div>

        <div class="panel-body">

            <?= $form->field($model, 'user_id')->dropdownList(User::getList(), ['prompt'=>'Select...']) ?>

            <?= $form->field($model, 'mounth')->dropDownList(Toolbar::month(),['prompt' => '','value'=>($model->isNewRecord  ? $filterDate['mounth']: $model->mounth)])?>

            <?= $form->field($model, 'year')->dropDownList(Payment::listRandom(),['prompt' => '','value'=>($model->isNewRecord  ?  $filterDate['year']: $model->year)])?>

            <?= $form->field($model, 'type')->dropdownList(Payment::getType(), ['prompt'=>'Select...','value'=>($model->isNewRecord  ? $filterDate['type']: $model->type)]) ?>

            <?= $form->field($model, 'amount')->textInput(['type'=>'number','step'=>'any']) ?>

            <?= $form->field($model, 'tax_profit')->textInput(['type'=>'number','step'=>'any']) ?>

            <?= $form->field($model, 'tax_war')->textInput(['type'=>'number','step'=>'any']) ?>

            <?= $form->field($model, 'tax_pension')->textInput(['type'=>'number','step'=>'any']) ?>

            <?= $form->field($model, 'payout')->textInput(['readonly' => true,'type'=>'number','step'=>'any','style'=>'background-color:#eee']) ?>


            <?=(!$model->isNewRecord  ?
                $form->field($model, 'created_at', ['options' => ['class' => 'hidden']])->textInput(['value' => !empty($calendar->created_at) ? date('Y-m-d', $calendar->created_at) : null])
                .$form->field($model, 'created_at')->widget(DatePicker::classname(), [
                    'clientOptions' => ['changeMonth' => true, 'changeYear' => true, 'yearRange' => $datePickerRange, 'altFormat' => 'yy-mm-dd', 'altField' => '#calendar-created_at'],
                    'options' => ['class' => 'form-control', 'readonly' => true, 'id' => 'date-pickercreated_at', 'name' => 'date-pickercreated_at','disabled'=>true,'style'=>'background-color:#eee']
                ]) :'')
            ?>

        </div>

    </div>

    <div class="pull-right">
        <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="clearfix"></div>

</div>