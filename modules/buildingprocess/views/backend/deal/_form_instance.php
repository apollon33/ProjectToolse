<?php
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
?>

<?= !empty($processTemplates) ? $this->render('_form_assing', [
    'modelPermission' => $modelPermission,
    'processTemplate' => $processTemplates,
    'processInstance' => $processInstance,
]) : ''; ?>

<?php $form = ActiveForm::begin([
    'id' => 'js-deal-stage-form',
    'action' => [Url::to(['update']), 'id' => $processInstance->id, 'type' => Yii::$app->request->get('type')],
    'options' => [
        'data-pjax' => true,
        'enctype' => 'multipart/form-data',
        'class' => 'form-horizontal menu-form js-deal-stage-form',
    ],
]); ?>

<?= $form->field($processInstance, 'process_id',['options' => ['class' => 'hidden']])->textInput(); ?>

<?php if (!empty($formStageChildren)) echo $formStageChildren; ?>

<?php ActiveForm::end(); ?>

