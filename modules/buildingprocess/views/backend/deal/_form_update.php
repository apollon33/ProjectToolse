<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Tabs;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use modules\document\models\TreeViewQMS;
use kartik\tree\Module;

use yii\web\View;
use yii\helpers\Json;

use modules\buildingprocess\models\ProcessTemplate;
use modules\buildingprocess\models\ProcessInstance;
use modules\buildingprocess\services\TabsService;

/* @var $this yii\web\View */
/* @var $model modules\buildingprocess\models\ProcessTemplate */
/* @var $modelDocument modules\document\models\Document */
/* @var $modelDiscussion modules\buildingprocess\models\Discussion */
/* @var $form yii\widgets\ActiveForm */
/* @var $formStageChildren [] array form stages children */
/* @var $validationFormStage [] array validation stages children */
/* @var $messagesArray [] array discussion messages */

$fieldOptions = [
    'template' => '{label}<div class="col-md-10 col-sm-10 col-md-offset-2">{input}{hint}{error}</div>',
    'labelOptions' => ['class' => 'control-label col-sm-2 col-md-2 col-xs-12'],
];
$this->title = 'Process Instance';
$this->params['breadcrumbs'][] = ['label' => 'Processes Instance', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$item = [];
$tabs = TabsService::getInactiveSteps($processInstance);
if (!empty($processInstances)) {
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
            'content' => $stage->id === $firstProcessInstance->id ? $this->render('_form_tab', [
                'stage' => $stage,
                'processInstance' => $processInstance,
                'formStageChildren' => $formStageChildren,
                'isAllowedToEditStage' => $isAllowedToEditStage
            ]) : '',
        ];
    }
    $item[] = [
        'label' => Yii::t('app', 'Discussion'),
        'active' => false,
        'headerOptions' => [
            'style' => 'display: block'
        ],
        'options' => [
            'id' => 'discussion'
        ],
        'content' =>  $this->render('_form_discussion',[
            'processInstance' => $processInstance,
            'formStageChildren' => $formStageChildren,
            'modelDiscussion' => $modelDiscussion,
            'messagesArray' => $messages,
            'userId' => $userId,
            'discussionMessagesId' => $discussionMessagesId
        ])
    ];


    foreach ($tabs as $tab) {
        $item[] = [
            'label' => Yii::t('app', $tab['label']),
            'active' => false,
            'headerOptions' => [
                'class' => 'no_active',
            ],
        ];
    }


    $validationFormStages = Json::encode($validationFormStage);
    $this->registerJS("BuildingProcessHelper.validationDeals($validationFormStages)",
        View::POS_READY);


    ?>

    <?php Modal::begin([
        'id' => 'document-modal',
        'header' => '<h4 class="modal-title">' . Yii::t('app', 'Document') . '</h4>',
        'toggleButton' => [
            'class' => 'document-button hidden',
        ],
        ]);?>
    <div class="document-body">
    </div>

    <div class="form-horizontal">
        <?= Html::a('Cancel', '#', ['class' => 'btn btn-primary link-popup-close']) ?>
        <?= Html::a('Save', '#',['class' => 'btn btn-success doc-p-i']) ?>
    </div>



    <?php Modal::end();?>

    <div class="building-process-create">

        <h1><?= Html::encode($this->title) ?></h1>

        <div class="building-process-form col-lg-8 alert alert-info">

            <div class="panel panel-default">

                <div class="panel-heading"><b><?= Yii::t('app', 'Processes Instance') ?></b></div>
                <?= Tabs::widget([
                    'options' => ['id' => 'js-deal-stages'],
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

    <?php
    $validationFormStage = Json::encode($validationFormStage);
    $this->registerJS("
    $('form input.date').datepicker();
    BuildingProcessHelper.validationDeals($validationFormStage)
    ", View::POS_READY);
} ?>



