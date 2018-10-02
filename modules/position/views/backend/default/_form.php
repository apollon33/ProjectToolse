<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use modules\department\models\Department;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="position-form col-lg-8 alert alert-info">

    <?php $form = ActiveForm::begin(); ?>

    <div class="panel panel-default">

        <div class="panel-heading"><b><?= Yii::t('app', 'Position'); ?></b></div>

        <div class="panel-body">

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]); ?>

            <?= $form->field($model, 'department_id')->dropdownList(Department::getList(), ['prompt' => Yii::t('app', ' -- Select Department -- ')]); ?>

            <?php echo !empty($referrer) ? Html::hiddenInput('referrer', $referrer) : null; ?>

        </div>

    </div>

    <div class="pull-right">
        <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-primary']); ?>
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-success']); ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="clearfix"></div>
</div>