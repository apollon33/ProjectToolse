<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var yii\web\View $this */
/* @var common\models\User $model */
/* @var string|null $activeTab */

$this->title = Yii::t('app', 'View User') . ': ' . $model->username;
$option = ['readOnly' => 'readOnly'];

?>

<div class="user-view">

  <h1><?= Html::encode($this->title) ?></h1>

  <div class="user-form col-lg-8 col-md-12 col-sm-12 alert alert-info">

      <?php $form = ActiveForm::begin([
          'options' => [
              'enctype' => 'multipart/form-data',
              'class' => 'form-horizontal',
          ]
      ]); ?>

    <div class="form-group">
      <p class="new">

      <div class="col-sm-12 copy-field">
        <div class="col-sm-10">
            <?= $form->field($model, 'email')->textInput($option) ?>
        </div>
          <?php if (! empty($model->email)): ?>
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
            <?= $form->field($model, 'first_name')->textInput($option) ?>
        </div>
          <?php if (! empty($model->email)): ?>
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
            <?= $form->field($model, 'last_name')->textInput($option) ?>
        </div>
          <?php if (! empty($model->email)): ?>
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
            <?= $form->field($model, 'englishLevel')->textInput($option) ?>
        </div>
          <?php if (! empty($model->englishLevel)): ?>
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
            <?= $form->field($model, 'positionAndGrade')->textInput($option) ?>
        </div>
          <?php if (! empty($model->positionAndGrade)): ?>
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
            <?= $form->field($model, 'performanceAppraisalReview')->textInput($option) ?>
        </div>
          <?php if (! empty($model->performanceAppraisalReview)): ?>
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
          <?php if (! empty($model->personalDevelopmentPlan)): ?>
            <div class="col-sm-2">
              <a class="btn btn-success copy-skill" href="#">
                <span class="glyphicon glyphicon-copy" aria-hidden="true"></span>
                  <?= Yii::t('app', 'Copy') ?>
              </a>
            </div>
          <?php endif; ?>
      </div>
    </div>

      <?php ActiveForm::end(); ?>

    <div class="clearfix"></div>
  </div>

</div>
