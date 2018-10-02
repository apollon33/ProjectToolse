<?php

use kartik\datecontrol\DateControl;
use yii\helpers\Html;
use kartik\grid\GridView;
use common\widgets\Gallery;
use common\helpers\Toolbar;
use common\models\User;
use modules\position\models\Position;
use yii\helpers\ArrayHelper;
use common\widgets\RoleColumn;

use common\access\AccessManager;

/* @var yii\web\View $this */
/* @var common\models\UserSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Users');
?>

<?= Gallery::widget() ?>

<div class="user-index">

  <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($data) {
            return ['id' => $data->id];
        },
        'filterModel' => $searchModel,
        'resizeStorageKey' => 'userGrid',
        'panel' => [
            'footer' => Html::tag('div', Toolbar::createButton(Yii::t('app', 'Add User')), ['class' => 'pull-left'])
                        . Toolbar::paginationSelect($dataProvider),
        ],
        'toolbar' => [
            Toolbar::toggleButton($dataProvider),
            Toolbar::refreshButton(),
            Toolbar::createButton(Yii::t('app', 'Add User')),
            Toolbar::deleteButtonUser(),
            Toolbar::activateSelect(),
            Toolbar::exportButton(),
        ],
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'contentOptions' => ['class' => 'skip-export'],
                'headerOptions' => ['class' => 'skip-export']
            ],
            [
                'options' => ['style' => 'width: 15px'],
                'value' => 'id',
            ],
            [
                'attribute' => 'imageThumbnailUrl',
                'format' => ['image', ['class' => 'img-thumbnail avatar']],
                'headerOptions' => ['class' => 'skip-export'],
                'contentOptions' => ['class' => 'skip-export'],
            ],
            'username',
            'email:email',
            'fullName',
            [
                'attribute' => 'position_id',
                'filter' => Position::getList(),
                'value' => 'logPosition.name',
            ],
            [
                'attribute' => 'createAt',
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
            ],
            [
                'attribute' => 'lastLogin',
                'options' => ['style' => 'width: 240px'],
                'filter' => Html::tag(
                    'div',
                    DateControl::widget([
                        'model' => $searchModel,
                        'attribute' => 'date_login_from',
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
                        'attribute' => 'date_login_to',
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
            ],
            [
                'attribute' => 'DateReceipt',
                'label' => Yii::t('app', 'Start of Employment'),
                //'options' => ['style'=>'width: 240px'],
                'filter' => Html::tag(
                    'div',
                    DateControl::widget([
                        'model' => $searchModel,
                        'attribute' => 'date_receipt_from',
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
                        'attribute' => 'date_receipt_to',
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
            ],
            [
                'class' => 'common\components\Column\SetColumn',
                'attribute' => 'active',
                'filter' => User::getActiveStatuses(),
                'cssClasses' => [
                    User::ACTIVE_YES => 'success',
                    User::ACTIVE_NO => 'danger',
                ],
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
                'buttons' => [
                    'view' => function ($url, $model) {
                        $options = [
                            'title' => Yii::t('app', 'View'),
                        ];
                        if ($model->id === Yii::$app->user->identity->id || Yii::$app->user->can('core.backend.admin:' . AccessManager::VIEW)) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                                [
                                    'view',
                                    'id' => $model->id,
                                    'params' => Yii::$app->request->queryParams ?: ['page' => 1]
                                ], $options);
                        }
                    },
                    'delete' => function ($url, $model) {
                        $expandButton = Html::tag('span', '', ['class' => 'glyphicon glyphicon-trash']);

                        return Html::a($expandButton, ['archive', 'id' => $model->id], [
                            'title' => Yii::t('app', 'Trash'),
                            'aria-label' => Yii::t('app', 'Trash'),
                        ]);
                    },
                    'update' => function ($url, $model) {
                        $options = [
                            'title' => Yii::t('app', 'Update'),
                        ];
                        if ($model->id === Yii::$app->user->identity->id || Yii::$app->user->can('core.backend.admin:' . AccessManager::UPDATE)) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                                [
                                    'update',
                                    'id' => $model->id,
                                    'params' => Yii::$app->request->queryParams ?: ['page' => 1]
                                ], $options);
                        }
                    },


                ],
                'template' => $this->render('@backend/views/layouts/_options', [
                    'options' => [
                        'view' => 'core.backend.admin:' . AccessManager::VIEW,
                        'update' => 'core.backend.user:' . AccessManager::UPDATE,
                        'delete' => 'core.backend.admin:' . AccessManager::DELETE
                    ],
                ]),
                'headerOptions' => ['class' => 'skip-export'],
                'contentOptions' => ['class' => 'skip-export'],
            ],
        ],
    ]); ?>

</div>
