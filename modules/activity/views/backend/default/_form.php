<?php

use modules\project\models\Project;
use common\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\activity\models\Activity */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="activity-form col-lg-8 alert alert-info">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal']]); ?>

    <div class="panel panel-default">

        <div class="panel-heading"><b><?= Yii::t('app', 'Activity') ?></b></div>

        <div class="panel-body">

            <?= $this->render('_screenshot', ['form' => $form, 'model' => $model]) ?>

            <?= $form->field($model, 'user_id')->dropDownList(User::getList()) ?>

            <?= $form->field($model, 'project_id')->dropDownList(Project::getList(), ['prompt' => Yii::t('app', 'Select Project')]) ?>

            <?= $form->field($model, 'target_window')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'keyboard_activity_percent')->textInput() ?>

            <?= $form->field($model, 'mouse_activity_percent')->textInput() ?>

            <?= $form->field($model, 'interval')->textInput() ?>

            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

            <?php if (!$model->isNewRecord): ?>

            <?= $form->field($model, 'created')->textInput(['disabled' => true]) ?>

            <?= $form->field($model, 'updated')->textInput(['disabled' => true]) ?>

            <?php endif; ?>

        </div>

    </div>

    <div class="pull-right">
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="clearfix"></div>

</div>