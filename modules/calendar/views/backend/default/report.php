<?php

use kartik\datecontrol\DateControl;
use yii\helpers\Html;
use kartik\grid\GridView;
use common\widgets\Gallery;
use common\helpers\Toolbar;
use common\models\User;
use common\models\UserSearch;

use common\access\AccessManager;

/* @var $this yii\web\View */
/* @var $searchModel modules\user\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
?>

<?= Gallery::widget() ?>

<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'resizeStorageKey' => 'profileGrid',
        'panel' => [
            'footer' =>
                 Toolbar::paginationSelect($dataProvider),
        ],
        'toolbar' => [
            Toolbar::toggleButton($dataProvider),
        ],
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'contentOptions' => ['class'=>'skip-export'],
                'headerOptions' => ['class'=>'skip-export']
            ],
            'id',
            'fullName',

            [
                'attribute' => 'DateReceipt',
                'options' => ['style'=>'width: 240px'],
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
                'attribute' => 'Hour',
                'options' => ['style'=>'width: 240px'],
                'filter' => Html::tag(
                    'div',
                    DateControl::widget([
                        'model' => $searchModel,
                        'attribute' => 'hour_start',
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
                        'attribute' => 'hour_end',
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
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'view' => function ($url, $model) {
                        $expandButton = Html::tag('span', '', ['class' => 'glyphicon glyphicon-eye-open']);
                        return Html::a($expandButton, ['view', 'id' => $model->id], [
                            'title' => Yii::t('app', 'Expand'),
                            'aria-label' => Yii::t('app', 'Expand'),
                        ]);
                    }
                ],
                'template' => $this->render('@backend/views/layouts/_options', [
                    'options' => [
                        'view' => 'calendar.backend.default:' . AccessManager::VIEW,
                    ],
                ]),
                'headerOptions' => ['class'=>'skip-export'],
                'contentOptions' => ['class'=>'skip-export'],
            ],
        ],
    ]); ?>

</div>