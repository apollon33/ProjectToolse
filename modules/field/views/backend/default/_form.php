<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use modules\field\models\ProcessFieldTemplate;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $model modules\buildingprocess\models\ProcessBuilding */
/* @var $form yii\widgets\ActiveForm */

$fieldOptions = [
    'template'     => '{label}<div class="col-md-10 col-sm-10 col-md-offset-2">{input}{hint}{error}</div>',
    'labelOptions' => ['class' => 'control-label col-sm-2 col-md-2 col-xs-12'],
];

$options = Json::decode($model->option);

?>

<?php $form = ActiveForm::begin([
    'action'  => Url::to([$path]),
    'options' => [
        'enctype' => 'multipart/form-data',
        'class'   => 'form-horizontal',
        'id'      => 'fieldCreate',
        'data'    => ['pjax' => true],
    ],
]); ?>

<div class="panel panel-default">

    <div class="panel-body">

        <?= $form->field($model, 'process_id')->textInput(['class' => 'hidden'])->label(false) ?>

        <?= $form->field($model, 'type_field')->dropDownList($selectTableFiled,
            ['prompt' => 'Select...', 'class' => 'form-control select-type-field']) ?>

        <div class="<?= $model->getOptionField() ? '' : 'hidden'; ?> col-md-12 option-field">

            <?php if (!empty($options)) :  $options  ?>
                <?php foreach ($options as $key => $option) : ?>
                    <div class="form-group optionfield">
                        <label class="control-label col-sm-2 col-md-2 col-xs-12" for="optionfield-name">Name</label>
                        <div class="col-md-9 col-sm-9">
                            <input type="text" id="optionfield-name" class="form-control" name="OptionField[]"
                                   maxlength="100" aria-required="true" aria-invalid="true" value="<?= $option ?>">
                        </div>
                        <div class="col-md-1 col-sm-1">
                            <buttom
                                class="btn btn-<?= $key ? 'danger' : 'primary' ?> refresh-link <?= $key ? 'delete' : 'add' ?>-option-field">
                                <i class="glyphicon glyphicon-<?= $key ? 'trash' : 'plus' ?>"></i>
                            </buttom>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="form-group optionfield select">
                    <label class="control-label col-sm-2 col-md-2 col-xs-12" for="optionfield-name">Name</label>
                    <div class="col-md-9 col-sm-9">
                        <input type="text" id="optionfield-name" class="form-control" name="OptionField[]"
                               maxlength="100" aria-required="true" aria-invalid="true">
                    </div>
                    <div class="col-md-1 col-sm-1">
                        <buttom class="btn btn-primary refresh-link add-option-field"><i
                                class="glyphicon glyphicon-plus"></i></buttom>
                    </div>

                </div>
                <div class="optionclient">
                    <label class="control-label col-sm-2 col-md-2 col-xs-12" for="optionfield-name">Name</label>
                    <div class="col-md-10 col-sm-10">

                        <select name="OptionField[]" id="optionfield-name" class="form-control">
                            <?php foreach($client_field as $k => $value) :?>
                                <option value="<?=$k;?>"><?=$value;?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>

            <?php endif; ?>

        </div>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'required', $fieldOptions)->checkbox() ?>

        <?= $form->field($model, 'modify', $fieldOptions)->checkbox() ?>

    </div>

</div>

<div class="pull-right ">
    <?= Html::a('Cancel', 'index', ['class' => 'btn btn-primary cancel-modal']) ?>
    <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>

<div class="clearfix"></div>