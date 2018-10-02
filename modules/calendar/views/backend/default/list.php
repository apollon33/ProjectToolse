<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\components\DurationConverter;
use common\helpers\Toolbar;
use modules\project\models\Project;
use modules\calendar\models\Calendar;
use common\models\User;
use kartik\datecontrol\DateControl;

use common\access\AccessManager;

/* @var $this yii\web\View */
/* @var $searchModel modules\profile\models\ProfileSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', ' List View');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profile-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'resizeStorageKey' => 'profileGrid',
        'panel' => [
            'footer' => Html::tag('div', Toolbar::createButton(Yii::t('app', 'Add Task')), ['class' => 'pull-left'])
                . Toolbar::paginationSelect($dataProvider),
        ],
        'toolbar' => [
            Toolbar::toggleButton($dataProvider),
            Toolbar::refreshButton(),
            Toolbar::createButton(Yii::t('app', 'Add Task')),
            Toolbar::deleteButton(),
            Toolbar::exportButton(),
        ],
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'headerOptions' => ['class'=>'skip-export'],
                'contentOptions' => ['class'=>'skip-export'],
            ],
            'id',
            [
                'attribute'=>'project_id',
                'filter' =>Project::getList(),
                'value'=>'project.project',
            ],
            [
                'attribute'=>'user_id',
                'format' => 'html',
                'filter'=> User::getList(),
                'value'=> 'user.fullName',
            ],
            [
                'attribute' => 'estimated_time',
                'value' => function ($date) {
                    return DurationConverter::shortDuration(Yii::$app->formatter->asDuration($date->estimated_time));
                },
            ],
            [
                'attribute' => 'TaskTime',
                'label'=> Yii::t('app', 'Hour'),
            ],
            [
                'label'=>Yii::t('app', 'Interval'),
                'attribute'=>'Interval',
                'filter' => Html::tag(
                    'div',
                    DateControl::widget([
                        'model' => $searchModel,
                        'attribute' => 'start_at_from',
                        'type' => DateControl::FORMAT_DATE,
                        'autoWidget' => [
                            'pickerButton' => false,
                        ],
                        'widgetOptions' => [
                            'layout' => '{remove}{input}',
                            'options' => ['placeholder' => Yii::t('app', 'From start')],
                        ],
                    ])
                    . DateControl::widget([
                        'model' => $searchModel,
                        'attribute' => 'start_at_to',
                        'type' => DateControl::FORMAT_DATE,
                        'autoWidget' => [
                            'pickerButton' => false,
                        ],
                        'widgetOptions' => [
                            'layout' => '{input}{remove}',
                            'options' => ['placeholder' => Yii::t('app', 'To start')],
                        ],
                    ]),
                    ['class' => 'date-range']
                ),
                'options' => [ 'style'=>'width: 300px'],
            ],
            [
                'label' => Yii::t('app', 'Approval'),
                'attribute' => 'estimate_approval',
                'format' => 'boolean',
                'filter' => Calendar::getApprovals(),
            ],
            [
                'attribute'=>'created_by',
                'filter'=> User::getList(),
                'value'=>'createdBy.fullName',
            ],


            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'value' => function () {
                    return GridView::ROW_COLLAPSED;
                },
                'enableRowClick' => true,
                'rowClickExcludedTags' => ['a', 'button', 'input', 'span'],
                'enableCache' => false,
                'detail' => function ($model) {
                    return $this->render('_detail', [
                        'model' => $model,
                    ]);
                },
                'expandOneOnly' => true,
                'detailOptions' => ['class' => 'detail-container'],
                'detailRowCssClass' => 'grid-detail',
                'detailAnimationDuration' => 'fast',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => $this->render('@backend/views/layouts/_options', [
                    'options' => [
                        'update' => 'calendar.backend.default:' . AccessManager::UPDATE,
                        'delete' => 'calendar.backend.default:' . AccessManager::DELETE
                    ],
                ]),
                'headerOptions' => ['class'=>'skip-export'],
                'contentOptions' => ['class'=>'skip-export'],
            ],

        ],
    ]); ?>
</div>
