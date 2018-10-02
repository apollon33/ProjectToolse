<?php

use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;


/* @var $this yii\web\View */
/* @var $model modules\buildingprocess\models\BuildingProcess */
/* @var $form yii\widgets\ActiveForm */

$viewTableField = '_table_field';

$fieldOptions = [
    'template' => '{label}<div class="col-md-10 col-sm-10 col-md-offset-2">{input}{hint}{error}</div>',
    'labelOptions' => ['class' => 'control-label col-sm-2 col-md-2 col-xs-12'],
];

$parent = $model->id;

?>

<?php Modal::begin([
    'id' => 'stages-modal',
    'header' => '<h4 class="modal-title">' . Yii::t('app', 'Stages') . '</h4>',
    'toggleButton' => [
        'class' => 'stages-button hidden',

        'data-target' => '#stages-modal',
    ],
    'clientOptions' => false,
]); ?>

<?php Pjax::begin([
    'id'              => 'formStages',
    'enablePushState' => false,
    'linkSelector'    => 'a.pjax-modal-stages',
]); ?>

<?php Pjax::end(); ?>

<?php Modal::end(); ?>

<?php Modal::begin([
    'id'            => 'field-modal',
    'header'        => '<h4 class="modal-title">' . Yii::t('app', 'Field') . '</h4>',
    'toggleButton'  => [
        'class'       => 'field-button hidden',
        'data-target' => '#field-modal',
    ],
    'clientOptions' => false,
]); ?>
<?php Pjax::begin([
    'id'              => 'formField',
    'enablePushState' => false,
    'linkSelector' => 'a.pjax-modal-field',
]); ?>

<?php Pjax::end(); ?>
<?php Modal::end(); ?>

<div class="building-process-form col-lg-8 alert alert-info">

    <?php $form = ActiveForm::begin(); ?>

    <div class="panel panel-default">

        <div class="panel-heading"><b><?= Yii::t('app', 'Building Process') ?></b></div>

        <div class="panel-body">

            <?= $form->field($model, 'title')->textInput([
                'maxlength' => true,
                'class' => 'form-control',
            ]) ?>


            <?= $form->field($model, 'type')->widget(Select2::classname(), [
                'data' => $model->listType,
                'options' => ['placeholder' => 'Select a type ...'],
                'pluginOptions' => [
                    'tags' => true,
                    'tokenSeparators' => [',', ' '],
                    'maximumInputLength' => 10
                ],
            ])->label(Yii::t('app', 'Type')); ?>

            <?= $form->field($model, 'display', $fieldOptions)->checkbox() ?>

            <?= $form->field($model, 'create_folder', $fieldOptions)->checkbox() ?>

            <?= $form->field($model, 'display_all_tabs', $fieldOptions)->checkbox() ?>

            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

            <?php if(!$model->isNewRecord ):?>
            <div class="alert panel-body">
                <div class="form-group pull-right">

                    <div class="col-sm-12">
                        <?= Html::a(Yii::t('app', 'Add Field'), ['/field/field', 'process_id' => $model->id], [
                                'class'      => 'btn btn-success pjax-modal-field add-field',
                                'title'      => Yii::t('app', 'Add New Record'),
                                'aria-label' => Yii::t('app', 'Add New Record'),
                            ]) ?>
                    </div>

                </div>

                <div class="col-sm-12">
                    <div id="show_table_field" class="alert alert-info">
                        <?=$this->render($viewTableField, ['dataProvider' => $dataTypeField]) ?>
                    </div>
                </div>


            <div class="form-group pull-right">
                <div class="col-sm-12">
                    <?= Html::a(Yii::t('app', 'Add Stages'), ['stages', 'parent' => $parent], [
                            'class' => 'btn btn-success pjax-modal-stages add-stages',
                            'title' => Yii::t('app', 'Add New Record'),
                            'aria-label' => Yii::t('app', 'Add New Record'),
                        ]) ?>
                </div>

            </div>
            <div class="col-sm-12">
                <div id="show_table_stages" class="alert alert-info">
                    <?= $this->render('_table_stages', ['dataProvider' => $dataProvider]) ?>
                </div>
            </div>
            </div>
            <?php endif;?>
        </div>

    </div>

    <div class="pull-right">
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="clearfix"></div>

</div>
