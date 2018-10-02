<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\User;
use modules\country\models\Country;
use yii\jui\DatePicker;
use common\widgets\Gallery;
use kartik\file\FileInput;

$this->params['breadcrumbs'][] = $this->title;

$fieldOptions = [
    'options' => ['class' => 'col-lg-6'],
    'inputOptions' => ['class' => 'form-control'],
];
$dateFieldOptions = ['options' => $fieldOptions['inputOptions'] + ['readonly' => true]];

$userAgeLimit  = 100;
$datePickerRange = (date('Y') - $userAgeLimit) . ':' . date('Y');
?>

<div class="section bg-white">

    <div class="container">

        <div class="col-sm-12">
            <h2 class="block-title"><?= Yii::t('app', 'Profile Settings') ?></h2>
        </div>

        <div class="panel-body">

            <?php $form = ActiveForm::begin(['id' => 'form-signup', 'options' => ['enctype' => 'multipart/form-data']]); ?>

            <div class="avatar-settings col-lg-12">

                    <?= Gallery::widget() ?>

                    <?= $form->field($model, 'avatar')->widget(FileInput::classname(), [
                        'pluginOptions' => [
                            'initialPreview' => [
                                !empty($model->avatar)
                                    ? '<a href="' . $model->imageUrl . '" title="' . $model->username . '" data-gallery>'
                                    . Html::img($model->imageThumbnailUrl, ['class' => 'img-preview', 'title' => $model->username, 'alt' => $model->username])
                                    . '<br />'
                                    . '</a>'
                                    : null
                            ],
                            'overwriteInitial' => true,
                            'pluginLoading' => true,
                            'showCaption' => false,
                            'showUpload' => false,
                            'showClose' => false,
                            'removeClass' => 'btn btn-sm btn-danger',
                            'browseClass' => 'btn btn-sm btn-info',
                            'browseLabel' => '',
                            'removeLabel' => '',
                            'browseIcon' => '<i class="glyphicon glyphicon-picture"></i>',
                            'previewTemplates' => [
                                'generic' => '<div class="file-preview-frame" id="{previewId}" data-fileIndex="{fileindex}">{content}</div>',
                                'image' => '<div class="file-preview-frame" id="{previewId}" data-fileIndex="{fileindex}"><img src="{data}" class="img-preview" title="{caption}" alt="{caption}"></div>',
                            ]
                        ],
                    ]) ?>

            </div>

            <?= $form->field($model, 'username', $fieldOptions)->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'email', $fieldOptions)->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'first_name', $fieldOptions)->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'last_name', $fieldOptions)->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'city', $fieldOptions)->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'country_id', $fieldOptions)->dropdownList(Country::getList(), ['prompt'=>'']) ?>

            <?= $form->field($model, 'birthday', ['options' => ['class' => 'hidden']])->textInput([
                'value' => !empty($model->birthday) ? date('Y-m-d', $model->birthday) : null
            ]) ?>

            <?= $form->field($model, 'birthday', $fieldOptions)->widget(DatePicker::classname(), [
                'clientOptions' => [
                    'changeMonth' => true,
                    'changeYear' => true,
                    'yearRange' => $datePickerRange,
                    'altFormat' => 'yy-mm-dd',
                    'altField' => '#user-birthday',
                ],
                'options' => [
                    'class' => 'form-control',
                    'readonly' => true,
                    'id' => 'date-picker',
                    'name' => 'date-picker'
                ]
            ]) ?>

            <?= $form->field($model, 'gender', $fieldOptions)->dropdownList(User::getGenders()) ?>

            <?= $form->field($model, 'address', $fieldOptions)->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'zip', $fieldOptions)->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'phone', $fieldOptions)->textInput(['maxlength' => true]) ?>

            <div class="col-lg-12">
                <br />
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary-magnet']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>

    </div>

</div>