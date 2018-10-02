<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Tabs;
use yii\widgets\ActiveForm;

use yii\web\View;
use yii\helpers\Json;

use modules\buildingprocess\models\ProcessTemplate;

/* @var $this yii\web\View */
/* @var $model modules\buildingprocess\models\ProcessTemplate */
/* @var $form yii\widgets\ActiveForm */
/* @var $formStageChildren [] array form stages children */
/* @var $validationFormStage [] array validation stages children */

$fieldOptions = [
    'template' => '{label}<div class="col-md-10 col-sm-10 col-md-offset-2">{input}{hint}{error}</div>',
    'labelOptions' => ['class' => 'control-label col-sm-2 col-md-2 col-xs-12'],
];
$this->title = 'Process Instance';
$this->params['breadcrumbs'][] = ['label' => 'Processes Instance', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$item = [];


if (!empty($processInstances)) :
    foreach ($processInstances as $key => $stage) {
        $item[] = [
            'label' => Yii::t('app', $stage->process->title),
            'active' => $stage->id === $firstProcessInstance->id ? true : false,
            'options' => [
                'id' => str_replace(' ', '-', strtolower($stage->process->title)),
            ],
            'url' => [
                'view',
                'type' => Yii::$app->request->get('type'),
                'tab' => $stage->id,
                'id' => Yii::$app->request->get('id'),
            ],
            'content' => '<div style="text-align: center">
                            <div class="alert alert-warning" role="alert">'.
                            $messageProcessInstance.
                        '</div></div>'
        ];

    }
    ?>

    <div class="building-process-create">

        <h1><?= Html::encode($this->title) ?></h1>

        <div class="building-process-form col-lg-8 alert alert-info">

            <div class="panel panel-default">

                <div class="panel-heading"><b><?= Yii::t('app', 'Processes Instance') ?></b></div>
                <?= Tabs::widget([
                    'items' => $item,
                    'itemOptions' => ['style' => ['padding-top' => '0']],
                    'clientOptions' => ['collapsible' => false],
                ]); ?>

                <div class="panel-body">

                </div>

            </div>

        </div>

        <div class="clearfix"></div>

    </div>
    <?php endif; ?>



