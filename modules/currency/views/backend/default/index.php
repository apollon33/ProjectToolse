<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\helpers\Toolbar;
use kartik\datecontrol\DateControl;

use common\access\AccessManager;


/* @var $this yii\web\View */
/* @var $searchModel modules\currency\models\CurrencySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Currencies');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="currency-index">

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
        'resizeStorageKey' => 'currencyGrid',
        'panel' => [
            'footer' => Html::tag('div', Toolbar::createButton(Yii::t('app', 'Refresh')), ['class' => 'pull-left'])
                . Toolbar::paginationSelect($dataProvider),
        ],
        'toolbar' => [
            Toolbar::toggleButton($dataProvider),
            Toolbar::refreshButton(),
            Toolbar::createButton(Yii::t('app', 'Refresh')),
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
            'key_digital',
            'key_letter',
            [
                'attribute'=>'date',
                'options' => ['style'=>'width: 240px'],
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
                    return date('Y-m-d',$data->date);
                },
            ],
            'name',
            [
                'attribute'=>'rate',
                'value' =>'Rate'
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
                ],*/
                'template' => $this->render('@backend/views/layouts/_options', [
                    'options' => [
                        'update' => 'currency.backend.default:' . AccessManager::UPDATE,
                        'show' => 'currency.backend.default:' . AccessManager::VIEW,
                        'delete' => 'currency.backend.default:' . AccessManager::DELETE
                    ],
                ]),
                'headerOptions' => ['class'=>'skip-export'],
                'contentOptions' => ['class'=>'skip-export'],
            ],
        ],
    ]); ?>
</div>
