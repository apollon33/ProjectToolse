<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use modules\country\models\Country;
use modules\currency\models\Currency;

?>



<?php $form = ActiveForm::begin(); ?>
    <?= $form->field($counterparties, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($counterparties, 'type')->textInput(['maxlength' => true]) ?>
    <?= $form->field($counterparties, 'registration_number')->textInput(['maxlength' => true]) ?>
    <?= $form->field($counterparties, 'vat')->textInput(['maxlength' => true]) ?>
    <?= $form->field($counterparties, 'timezone')->textInput(['maxlength' => true]) ?>
    <?= $form->field($counterparties, 'country')->dropDownList(Country::getList(),['prompt' => 'Select...']) ?>
    <?= $form->field($counterparties, 'city')->textInput(['maxlength' => true]) ?>
    <?= $form->field($counterparties, 'address')->textInput(['maxlength' => true]) ?>
    <?= $form->field($counterparties, 'payment_method')->textInput(['maxlength' => true]) ?>
    <?= $form->field($counterparties, 'currency')->dropDownList(Currency::getList(), ['prompt' => 'Select...']) ?>
    <?= $form->field($counterparties, 'bank_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($counterparties, 'iban')->textInput(['maxlength' => true]) ?>
    <?= $form->field($counterparties, 'swift')->textInput(['maxlength' => true]) ?>
    <?= $form->field($counterparties, 'comments')->textarea() ?>

<div class="pull-right">
    <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-primary']) ?>
    <?= Html::submitButton($counterparties->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-success']) ?>
</div>
<?php ActiveForm::end(); ?>

<div class="clearfix"></div>