<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \modules\holidayconfig\models\HolidayConfig;
use common\helpers\Toolbar;

/* @var $this yii\web\View */
/* @var $model modules\holidayconfig\models\HolidayConfig */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="holiday-config-form col-lg-8 alert alert-info">

    <?php $form = ActiveForm::begin(); ?>

    <div class="panel panel-default">

        <div class="panel-heading"><b><?= Yii::t('app', 'Holiday Config') ?></b></div>

        <div class="panel-body">

            <?= $form->field($model, 'month')->dropDownList(Toolbar::month(),['prompt' => ''])?>

            <?= $form->field($model, 'day')->dropDownList(Toolbar::listRandom(range(0, 31)),['prompt' => ''])?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        </div>

    </div>

    <div class="pull-right">
        <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="clearfix"></div>

</div>