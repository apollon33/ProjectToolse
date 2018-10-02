<?php

use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use modules\country\models\Country;

/* @var $this yii\web\View */
/* @var $model modules\client\models\Client */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="profile-form col-lg-8 alert alert-info">


    <?php $form = ActiveForm::begin([
        'options' => [
            'enctype' => 'multipart/form-data',
            'class' => 'form-horizontal'
        ]
    ]); ?>

    <?php $activeTab = ! empty($activeTab) ? $activeTab : 'general'; ?>

    <?php
    $buttonsSection = '<div class="pull-right">' .
                      Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-primary']) .
                      ' ' . Html::submitButton($client->isNewRecord ? Yii::t('app', 'Create') :
            Yii::t('app', 'Update'), ['class' => 'btn btn-success']) . '</div>';
    ?>

    <?= Tabs::widget([
        'items' => [
            [
                'label' => Yii::t('app', 'General'),
                'active' => empty($activeTab) || $activeTab == 'general',
                'options' => ['id' => 'general'],
                'content' =>
                    '<div class="form-group"><div class="col-sm-12">'
                    . $form->field($client, 'client_name')->textInput(['maxlength' => true])
                    . $form->field($client, 'company_name')->textInput(['maxlength' => true])
                    . $form->field($client, 'email')->textInput(['maxlength' => true])
                    . $form->field($client, 'skype')->textInput(['maxlength' => true])
                    . $form->field($client, 'phone')->textInput(['maxlength' => true])
                    . $form->field($client, 'country_id')->dropDownList(Country::getList(), ['prompt' => 'Select...'])
                    . $form->field($client, 'timezone')->textInput(['maxlength' => true])
                    . $form->field($client, 'description')->textarea(['rows' => 6])
                    . $buttonsSection . '</div></div>'
            ],
            [
                'label' => Yii::t('app', 'Counterparty information'),
                'active' => empty($activeTab) || $activeTab == 'counterparty_information',
                'option' => ['id' => 'counterparty_information'],
                'content' => $this->render('_form_counterparty', ['form' => $form, 'counterparties' => $counterparties])
            ]

        ],
    ]); ?>

    <?php ActiveForm::end(); ?>

  <div class="clearfix"></div>

</div>