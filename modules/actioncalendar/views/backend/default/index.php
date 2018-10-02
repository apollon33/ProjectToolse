<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\helpers\Toolbar;
use modules\holiday\models\Holiday;
use modules\actioncalendar\models\Event;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */
/* @var $searchModel modules\actioncalendar\models\EventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Events';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-index">

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
        'resizeStorageKey' => 'eventGrid',
        'panel' => [
            'footer' => Html::tag('div', Toolbar::createButton(Yii::t('app', 'Add Event')), ['class' => 'pull-left'])
                . Toolbar::paginationSelect($dataProvider),
        ],
        'toolbar' => [
            Toolbar::toggleButton($dataProvider),
            Toolbar::refreshButton(),
//            Toolbar::createButton(Yii::t('app', 'Add Event')),
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
            'name',
            [
                'attribute' => 'type',
                'filter' => Holiday::getTypeHoliday(),
                'value' => 'Type'
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
                'attribute' => 'active',
                'format' => 'boolean',
                'filter' => Event::getActiveStatuses(),
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                /*'buttons' => [
                    'show' => function ($url, $model) {
                        if ($model->visible) {
                            $options = ['title' => Yii::t('app', 'Disable')];
                            $iconClass = 'glyphicon-unlock';
                        } else {
                            $options = ['title' => Yii::t('app', 'Enable')];
                            $iconClass = 'glyphicon-lock';
                        }
                        return Html::a('<span class="glyphicon ' . $iconClass . '"></span>', $url, $options);
                    },
                ],
                'template' => $this->render('@backend/views/layouts/_options', [
                    'options' => ['update', 'show', 'delete'],
                ]),*/
                'headerOptions' => ['class'=>'skip-export'],
                'contentOptions' => ['class'=>'skip-export'],
            ],
        ],
    ]); ?>
</div>
