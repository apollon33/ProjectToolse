<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\profile\models\ProfileSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="profile-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'first_name') ?>

    <?= $form->field($model, 'last_name') ?>

    <?= $form->field($model, 'rate') ?>

    <?php // echo $form->field($model, 'login') ?>

    <?php // echo $form->field($model, 'password') ?>

    <?php // echo $form->field($model, 'email_profile') ?>

    <?php // echo $form->field($model, 'email_password') ?>

    <?php // echo $form->field($model, 'skype_profile') ?>

    <?php // echo $form->field($model, 'skype_password') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'verification') ?>

    <?php // echo $form->field($model, 'note') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
