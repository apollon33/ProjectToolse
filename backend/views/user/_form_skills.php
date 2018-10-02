<?php

use yii\widgets\ActiveForm;
use common\models\User;
use common\access\AccessManager;

/* @var $this yii\web\View */
/* @var $model User */
/* @var $form ActiveForm */

$option = Yii::$app->user->can('core.backend.admin:' . AccessManager::UPDATE) ? [] : ['disabled' => true];

?>
<div class="form-group">
    <p class="new">
    <div class="col-sm-12 copy-field">
        <div class="col-sm-10">
            <?= $form->field($model, 'englishLevel')->textInput($option) ?>
        </div>
        <?php if (!empty($model->englishLevel)): ?>
            <div class="col-sm-2">
                <a class="btn btn-success copy-skill" href="#" >
                    <span class="glyphicon glyphicon-copy" aria-hidden="true"></span>
                    <?= Yii::t('app', 'Copy') ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-sm-12 copy-field">
        <div class="col-sm-10">
            <?= $form->field($model, 'positionAndGrade')->textInput($option) ?>
        </div>
        <?php if (!empty($model->positionAndGrade)): ?>
            <div class="col-sm-2">
                <a class="btn btn-success copy-skill" href="#" >
                    <span class="glyphicon glyphicon-copy" aria-hidden="true"></span>
                    <?= Yii::t('app', 'Copy') ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-sm-12 copy-field">
        <div class="col-sm-10">
            <?= $form->field($model, 'performanceAppraisalReview')->textInput($option) ?>
        </div>
        <?php if (!empty($model->performanceAppraisalReview)): ?>
            <div class="col-sm-2">
                <a class="btn btn-success copy-skill" href="#">
                    <span class="glyphicon glyphicon-copy" aria-hidden="true"></span>
                    <?= Yii::t('app', 'Copy') ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-sm-12 copy-field">
        <div class="col-sm-10">
            <?= $form->field($model, 'personalDevelopmentPlan')->textInput($option) ?>
        </div>
        <?php if (!empty($model->personalDevelopmentPlan)): ?>
            <div class="col-sm-2">
                <a class="btn btn-success copy-skill" href="#">
                    <span class="glyphicon glyphicon-copy" aria-hidden="true"></span>
                    <?= Yii::t('app', 'Copy') ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-sm-12 copy-field">
        <div class="col-sm-10">
            <?= $form->field($model, 'personal_meeting')->textarea([
                'readonly' => !Yii::$app->user->can('core.backend.admin:' . AccessManager::UPDATE),
                'rows' => 6,
            ]) ?>
        </div>
    </div>
</div>