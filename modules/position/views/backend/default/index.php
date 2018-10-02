<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\helpers\Toolbar;
use modules\department\models\Department;

use common\access\AccessManager;
/* @var $this yii\web\View */
/* @var $searchModel modules\position\models\PositionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Positions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="position-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'sortable-table',
        ],
        'rowOptions' => function ($data) {
            return ['id' => $data->id];
        },
        'resizeStorageKey' => 'positionGrid',
        'panel' => [
            'footer' => Html::tag('div', Toolbar::createButton(Yii::t('app', 'Add Position')), ['class' => 'pull-left'])
                . Toolbar::paginationSelect($dataProvider),
        ],
        'toolbar' => [
            Toolbar::toggleButton($dataProvider),
            Toolbar::refreshButton(),
            Toolbar::createButton(Yii::t('app', 'Add Position')),
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
            [
                'attribute' => 'id',
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'name',
                'pageSummary' => true,
                'format' => 'ntext',
                'editableOptions' => function ($model, $key, $index) {
                    return [
                        'size' => 'lg',
                        'formOptions' => ['action' => ['update-position']],
                    ];
                }
            ],
            [
                'attribute' => 'department.name',
                'label' => 'Department',
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => $this->render('@backend/views/layouts/_options', [
                    'options' => [
                        'update' => 'position.backend.default:' . AccessManager::UPDATE,
                        'delete' => 'position.backend.default:' . AccessManager::DELETE
                    ],
                ]),
                'headerOptions' => ['class'=>'skip-export'],
                'contentOptions' => ['class'=>'skip-export'],
            ],
        ],
    ]); ?>
</div>
