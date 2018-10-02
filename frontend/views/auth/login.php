<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \modules\user\forms\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\authclient\widgets\AuthChoice;

$this->title = Yii::t('app', 'Login');
$this->params['breadcrumbs'][] = $this->title;

$fieldOptions = [
    'inputOptions' => ['class' => 'form-control input-lg'],
];
?>

<p>
    <?= Yii::t('app', 'Please fill out the following fields to login:') ?>
</p>

<div class="site-login">

    <div class="col-lg-8 col-lg-offset-2 alert alert-info">

        <div class="alert alert-danger">
            <?= Yii::t('app', 'Demo user credentials for examination') ?>:<br />
            <?= Yii::t('app', 'Username/password') ?>: <b>demo/demo</b>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <?= $form->field($model, 'username', $fieldOptions) ?>

            <?= $form->field($model, 'password', $fieldOptions)->passwordInput() ?>

            <?= $form->field($model, 'rememberMe')->checkbox() ?>

            <div class="note">
                <?= Yii::t('app', 'If you forgot your password you can') ?> <?= Html::a(Yii::t('app', 'reset it'), ['request-password-reset']) ?>.
            </div>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Login'), ['class' => 'btn btn-lg btn-primary', 'name' => 'login-button']) ?>
            </div>

            <br />

            <div class="panel panel-default">

                <div class="panel-heading"><b><?= Yii::t('app', 'Login via Networks') ?></b></div>

                <div class="panel-body">
                    <?= AuthChoice::widget([
                        'baseAuthUrl' => ['/site/auth'],
                        'popupMode' => false,
                    ]) ?>
                </div>

            </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>