<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use yii\bootstrap\Tabs;
use kartik\money\MaskMoney;
use modules\country\models\Country;

/* @var $this yii\web\View */
/* @var $model modules\profile\models\Profile */
/* @var $form yii\widgets\ActiveForm */
$session = Yii::$app->session;
$filterDate = $session->get('filterDate');
?>

<div class="profile-form col-lg-8 alert alert-info">


    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal']]); ?>



    <?php $activeTab = !empty($activeTab) ? $activeTab : 'general'; ?>

    <?= Tabs::widget([
        'items' => [
            [
                'label' => Yii::t('app', 'General'),
                'active' => empty($activeTab) || $activeTab == 'general',
                'options' => ['id' => 'general'],
                'content' =>
                    '<div class="form-group"><div class="col-sm-12">'
                    . $form->field($model, 'name')->textInput(['maxlength' => true,'placeholder'=>'Name',])
                    .'</div><div class="col-sm-6">'
                    . $form->field($model, 'first_name',[
                        'template' => '{label}<div class="col-sm-8">{input}{error}{hint}</div>',
                        'labelOptions' => ['class' => 'control-label  col-sm-4 '],
                    ])->textInput(['maxlength' => true,'placeholder'=>'First name'])
                    .'</div><div class="col-sm-6">'
                    . $form->field($model, 'last_name',[
                        'template' => '{label}<div class="col-sm-8">{input}{error}{hint}</div>',
                        'labelOptions' => ['class' => 'control-label  col-sm-4 '],
                    ])->textInput(['maxlength' => true,'placeholder'=>'Last name'])
                    .'</div><div class="col-sm-12">'
                    . $form->field($model, 'rate')->widget(MaskMoney::classname(), ['pluginOptions' => ['prefix' => '$ ', 'allowNegative' => false]])
                    .'</div><div class="col-sm-6">'
                    . $form->field($model, 'login',[
                        'template' => '{label}<div class="col-sm-8">{input}{error}{hint}</div>',
                        'labelOptions' => ['class' => 'control-label  col-sm-4 '],
                    ])->textInput(['maxlength' => true,'placeholder'=>'Login'])
                    .'</div><div class="col-sm-6">'
                    . $form->field($model, 'password',[
                        'template' => '{label}<div class="col-sm-8">{input}{error}{hint}</div>',
                        'labelOptions' => ['class' => 'control-label  col-sm-4 '],
                    ])->textInput(['maxlength' => true,'placeholder'=>'Password'])
                    .'</div><div class="col-sm-6">'
                    . $form->field($model, 'email',[
                        'template' => '{label}<div class="col-sm-8">{input}{error}{hint}</div>',
                        'labelOptions' => ['class' => 'control-label  col-sm-4 '],
                    ])->textInput(['maxlength' => true,'placeholder'=>'Email profile'])
                    .'</div><div class="col-sm-6">'
                    . $form->field($model, 'email_password',[
                        'template' => '{label}<div class="col-sm-8">{input}{error}{hint}</div>',
                        'labelOptions' => ['class' => 'control-label  col-sm-4 '],
                    ])->textInput(['maxlength' => true,'placeholder'=>'Email password'])
                    .'</div><div class="col-sm-6">'
                    . $form->field($model, 'skype',[
                        'template' => '{label}<div class="col-sm-8">{input}{error}{hint}</div>',
                        'labelOptions' => ['class' => 'control-label  col-sm-4 '],
                    ])->textInput(['maxlength' => true,'placeholder'=>'Skype profile'])
                    .'</div><div class="col-sm-6">'
                    . $form->field($model, 'skype_password',[
                        'template' => '{label}<div class="col-sm-8">{input}{error}{hint}</div>',
                        'labelOptions' => ['class' => 'control-label  col-sm-4 '],
                    ])->textInput(['maxlength' => true,'placeholder'=>'Skype password'])
                    .'</div><div class="col-sm-12">'
                    . $form->field($model, 'description')->textInput(['maxlength' => true,'placeholder'=>'Description'])
                    .'</div><div class="col-sm-12">'
                    . $form->field($model, 'verification')->textInput(['maxlength' => true,'placeholder'=>'Verification'])
                    .'</div><div class="col-sm-12">'
                    . $form->field($model, 'note')->textarea(['rows' => 6,'placeholder'=>'Note'])
                    .'</div></div>'

            ],

        ],
    ]);?>

    <div class="pull-right">
        <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>



    <div class="clearfix"></div>

</div>