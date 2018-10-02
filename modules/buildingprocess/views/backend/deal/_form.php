<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

use modules\buildingprocess\models\ProcessTemplate;

/* @var $this yii\web\View */
/* @var $model modules\buildingprocess\models\ProcessTemplate */
/* @var $form yii\widgets\ActiveForm */

$fieldOptions = [
    'template' => '{label}<div class="col-md-10 col-sm-10 col-md-offset-2">{input}{hint}{error}</div>',
    'labelOptions' => ['class' => 'control-label col-sm-2 col-md-2 col-xs-12'],
];


?>

<div class="building-process-form col-lg-8 alert alert-info">

    <?php $form = ActiveForm::begin([
        'action' => ['update', 'type' => Yii::$app->request->get('type')],
        'options' => [
            'enctype' => 'multipart/form-data',
            'class' => 'form-horizontal menu-form',
        ],
    ]); ?>
    <div class="panel panel-default">

        <div class="panel-heading"><b><?= Yii::t('app', 'Processes Instance') ?></b></div>

        <div class="panel-body">
            <?= $form->field($model, 'process_id')->dropDownList(ProcessTemplate::getList(),[
                'prompt' => 'Select...',
                'class' => 'form-control selectProcessId',
                'disabled' => !$model->isNewRecord ? true : false,
            ]);?>


            <div class="panel-body fields">
                <?= !empty($processField) ? ($this->render('_form_deal_update', ['processField' => $processField])) : '' ?>

            </div>

        </div>

    </div>

    <?php ActiveForm::end(); ?>

</div>

<div class="clearfix"></div>

</div>
