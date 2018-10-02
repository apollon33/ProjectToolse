<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\buildingprocess\models\BuildingProcess */
/* @var $form yii\widgets\ActiveForm */

$fieldOptions = [
    'template' => '{label}<div class="col-md-10 col-sm-10 col-md-offset-2">{input}{hint}{error}</div>',
    'labelOptions' => ['class' => 'control-label col-sm-2 col-md-2 col-xs-12'],
];

$viewTableField = '@modules/field/views/backend/default/_table_field';

?>
<?php $form = ActiveForm::begin([
    'action' => Url::to([$path]),
    'options' => [
        'enctype' => 'multipart/form-data',
        'class' => 'form-horizontal',
        'id' => 'stagesCreate',
        'data' => ['pjax' => true],
    ],
]); ?>

<div class="panel panel-default">

    <div class="panel-body">

        <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'class' => 'record-name form-control']) ?>

        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'parent')->textInput(['class' => 'hidden'])->label(false) ?>

        <h3><?= Html::encode($this->title) ?></h3>
        <?php if(!$model->isNewRecord):?>
        <div class="form-group pull-right">

            <div class="col-sm-12">
                <?= Html::a(Yii::t('app', 'Add Field'), ['/field/field-stage', 'process_id' => $model->id], [
                        'class' => 'btn btn-success pjax-modal-field add-field',
                        'title' => Yii::t('app', 'Add New Record'),
                        'aria-label' => Yii::t('app', 'Add New Record'),
                    ]) ?>
            </div>

        </div>

        <div class="col-sm-12">
            <div id="show_table_field" class="alert alert-info">
                <?= !$model->isNewRecord ? $this->render($viewTableField,
                    ['dataProvider' => $dataTypeField]) : '' ?>
            </div>
        </div>
        <?php endif;?>


    </div>

</div>

<div class="pull-right ">
    <?= Html::a('Cancel', 'index', ['class' => 'btn btn-primary cancel-modal']) ?>
    <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>

<div class="clearfix"></div>
