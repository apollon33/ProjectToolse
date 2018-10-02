<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use modules\field\models\ProcessFieldTemplate;

/* @var $this yii\web\View */
/* @var $model modules\buildingprocess\models\BuildingProcess */
/* @var $form yii\widgets\ActiveForm */

$fieldOptions = [
    'template' => '{label}<div class="col-md-10 col-sm-10 col-md-offset-2">{input}{hint}{error}</div>',
    'labelOptions' => ['class' => 'control-label col-sm-2 col-md-2 col-xs-12'],
];


?>

<?php $form = ActiveForm::begin([
    'action' => Url::to([$path]),
    'options' => [
        'enctype' => 'multipart/form-data',
        'class' => 'form-horizontal',
        'id' => 'fieldCreate',
        'data' => ['pjax' => true],
    ],
]); ?>

<div class="panel panel-default">

    <div class="panel-body">

        <?= $form->field($model, 'process_id')->textInput(['class' => 'hidden'])->label(false) ?>

        <?= $form->field($model, 'type_field')->dropDownList(ProcessFieldTemplate::getTypeField()) ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'required', $fieldOptions)->checkbox() ?>

        <?= $form->field($model, 'modify', $fieldOptions)->checkbox() ?>

    </div>

</div>

<div class="pull-right ">
    <?= Html::a('Cancel', 'index', ['class' => 'btn btn-primary cancel-modal-stages']) ?>
    <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>

<div class="clearfix"></div>