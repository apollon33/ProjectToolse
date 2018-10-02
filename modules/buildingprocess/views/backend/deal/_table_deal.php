<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\helpers\Toolbar;
use kartik\datecontrol\DateControl;

use common\models\User;
use modules\buildingprocess\models\ProcessTemplate;
use modules\buildingprocess\models\ProcessInstance;
use common\access\AccessManager;

/* @var $this yii\web\View */
/* @var $searchModel modules\buildingprocess\models\ProcessInstanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Deal - ' . Yii::$app->request->get('type');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="building-process-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'resizeStorageKey' => 'building-processGrid',
        'panel' => [
            'footer' => Toolbar::paginationTrashDeal($dataProvider),
        ],
        'toolbar' => [
            Toolbar::toggleButton($dataProvider),
            Toolbar::refreshButton(),
            Toolbar::createButtonDeal(Yii::t('app', 'Add New Deal')),
            Toolbar::exportButton(),
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'name',
                'value' => 'processFieldInstance.data'
            ],
            [
                'attribute' => 'process_id',
                'filter' => ProcessTemplate::getList(),
                'value' => 'process.title',
            ],
            [
                'attribute' => 'created_at',
                'options' => ['style' => 'width: 240px'],
                'filter' => Html::tag(
                    'div',
                    DateControl::widget([
                        'model' => $searchModel,
                        'attribute' => 'date_from',
                        'type' => DateControl::FORMAT_DATE,
                        'autoWidget' => [
                            'pickerButton' => false,
                        ],
                        'widgetOptions' => [
                            'layout' => '{remove}{input}',
                            'options' => ['placeholder' => Yii::t('app', 'From date')],
                        ],
                    ])
                    . DateControl::widget([
                        'model' => $searchModel,
                        'attribute' => 'date_to',
                        'type' => DateControl::FORMAT_DATE,
                        'autoWidget' => [
                            'pickerButton' => false,
                        ],
                        'widgetOptions' => [
                            'layout' => '{input}{remove}',
                            'options' => ['placeholder' => Yii::t('app', 'To date')],
                        ],
                    ]),
                    ['class' => 'date-range']
                ),
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->created_at);
                },
            ],
            [
                'attribute' => 'updated_at',
                'options' => ['style' => 'width: 240px'],
                'filter' => Html::tag(
                    'div',
                    DateControl::widget([
                        'model' => $searchModel,
                        'attribute' => 'update_from',
                        'type' => DateControl::FORMAT_DATE,
                        'autoWidget' => [
                            'pickerButton' => false,
                        ],
                        'widgetOptions' => [
                            'layout' => '{remove}{input}',
                            'options' => ['placeholder' => Yii::t('app', 'From date')],
                        ],
                    ])
                    . DateControl::widget([
                        'model' => $searchModel,
                        'attribute' => 'update_to',
                        'type' => DateControl::FORMAT_DATE,
                        'autoWidget' => [
                            'pickerButton' => false,
                        ],
                        'widgetOptions' => [
                            'layout' => '{input}{remove}',
                            'options' => ['placeholder' => Yii::t('app', 'To date')],
                        ],
                    ]),
                    ['class' => 'date-range']
                ),
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->updated_at);
                },
            ],
            [
                'attribute' => 'owner',
                'filter' => User::getList(),
                'value' => 'owners.fullName',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'update' => function ($url, $model) {

                        $options = [
                            'title' => Yii::t('app', 'Update'),
                        ];

                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                            ['view', 'type' => Yii::$app->request->get('type'), 'id' => $model->id], $options);
                    },
                    'delete' => function ($url, $model) {

                        $options = [
                            'title' => Yii::t('app', 'Delete'),
                        ];

                        return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                            ['delete', 'type' => Yii::$app->request->get('type'), 'id' => $model->id, 'page' => Yii::$app->request->get('page')], $options);
                    },

                ],
                'template' => $this->render('@backend/views/layouts/_options', [
                    'options' => [
                        'update' => 'buildingprocess.backend.deal:' . AccessManager::UPDATE,
                        'delete' => 'buildingprocess.backend.deal:' . AccessManager::DELETE
                    ],
                ]),
                'headerOptions' => ['class' => 'skip-export'],
                'contentOptions' => ['class' => 'skip-export'],
            ],
        ],

    ]); ?>
</div>

