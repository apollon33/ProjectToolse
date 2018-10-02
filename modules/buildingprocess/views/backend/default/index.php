<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\helpers\Toolbar;
use modules\buildingprocess\models\ProcessTemplate;

use common\access\AccessManager;

/* @var $this yii\web\View */
/* @var $searchModel modules\buildingprocess\models\ProcessBuildingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Building Processes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="building-process-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        /*'tableOptions' => [
            'class' => 'sortable-table',
        ],
        'rowOptions' => function ($data) {
            return ['id' => $data->id];
        },*/
        'resizeStorageKey' => 'building-processGrid',
        'panel' => [
            'footer' =>
                 Toolbar::paginationSelect($dataProvider),
        ],
        'toolbar' => [
            Toolbar::toggleButton($dataProvider),
            Toolbar::refreshButton(),
            Toolbar::createButtonBiuldingProcess(Yii::t('app', 'Add Building Process')),
            Toolbar::deleteButton(),
            //Toolbar::showSelect(),
            Toolbar::exportButton(),
        ],
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'headerOptions' => ['class'=>'skip-export'],
                'contentOptions' => ['class'=>'skip-export'],
            ],
            'id',
            'title',
            'type',
            [
                'class' => 'common\components\Column\SetColumn',
                'attribute' => 'display',
                'filter' => ProcessTemplate::getDisplayStatuses(),
                'cssClasses' => [
                    ProcessTemplate::DISPLAY_YES => 'success',
                    ProcessTemplate::DISPLAY_NO => 'danger',
                ],
            ],
            [
                'class' => 'common\components\Column\SetColumn',
                'attribute' => 'create_folder',
                'filter' => ProcessTemplate::getCreateFolderStatuses(),
                'cssClasses' => [
                    ProcessTemplate::CREATE_FOLDER_YES => 'success',
                    ProcessTemplate::CREATE_FOLDER_NO => 'danger',
                ],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => $this->render('@backend/views/layouts/_options', [
                    'options' => [
                        'update' => 'buildingprocess.backend.default:' . AccessManager::UPDATE,
                        'delete' => 'buildingprocess.backend.default:' . AccessManager::DELETE
                    ],

                ]),
                'headerOptions' => ['class'=>'skip-export'],
                'contentOptions' => ['class'=>'skip-export'],
            ],
        ],
    ]); ?>
</div>
