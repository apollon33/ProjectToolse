<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;

use modules\buildingprocess\models\ProcessTemplate;

use modules\document\models\Permission;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model modules\buildingprocess\models\ProcessTemplate */
/* @var $form yii\widgets\ActiveForm */

/* @var $model modules\document\models\Permission */

$fieldOptions = [
    'template' => '{label}<div class="col-md-10 col-sm-10 col-md-offset-2">{input}{hint}{error}</div>',
    'labelOptions' => ['class' => 'control-label col-sm-2 col-md-2 col-xs-12'],
];

?>

<?php $form = ActiveForm::begin([
    'action' => [Url::to('create'), 'type' => Yii::$app->request->get('type')],
    'options' => [
        'enctype' => 'multipart/form-data',
        'class' => 'form-horizontal menu-form',
        'data' => ['pjax' => true],
    ],
]); ?>
<div class="panel panel-default">

    <div class="panel-heading"><b><?= Yii::t('app', 'Processes Instance') ?></b></div>

    <div class="panel-body">
        <?= $form->field($processInstance, 'process_id')->dropDownList(ProcessTemplate::getList(), [
            'prompt' => 'Select...',
            'class' => 'form-control selectProcessId',
            'disabled' => !$processInstance->isNewRecord ? true : false,
        ]); ?>


        <div class="fields">
            <?= !empty($formStageChildren) ? $formStageChildren : '' ?>

        </div>

    </div>

</div>

<?php ActiveForm::end(); ?>
