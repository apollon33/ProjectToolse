<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\helpers\Toolbar;
use kartik\select2\Select2;
use modules\payment\models\Payment;
use kartik\datecontrol\DateControl;
use common\models\User;

use common\access\AccessManager;

/* @var $this yii\web\View */
/* @var $searchModel modules\payment\models\PaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Payments');
$this->params['breadcrumbs'][] = $this->title;
$userAgeLimit  = 100;
?>
<div class="payment-index">

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
        'resizeStorageKey' => 'paymentGrid',
        'panel' => [
            'footer' => Html::tag('div', Toolbar::createButton(Yii::t('app', 'Add Payment')), ['class' => 'pull-left'])
                . Toolbar::paginationSelect($dataProvider),
        ],
        'toolbar' => [
            Toolbar::toggleButton($dataProvider),
            Toolbar::refreshButton(),
            Toolbar::createButton(Yii::t('app', 'Add Payment')),
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
            [
                'attribute' => 'mounth',
                'filter' => Toolbar::month(),
                'value' => function ($data) {
                    return Toolbar::viewMonth($data->mounth);
                },
            ],
            [
                'attribute' => 'year',
                'filter' => Payment::listRandom(),
                'value' =>  'year',
            ],
            [
                'label'=>Yii::t('app', 'Type'),
                'attribute'=>'type',
                'filter' => Payment::getType(),
                'value' => 'Payment'
            ],
            [
                'label'=>Yii::t('app', 'User'),
                'attribute'=>'user_id',
                'filter' => User::getList(),
                'value' => 'user.fullName'
            ],
            'amount',
            'tax_profit',
            'tax_war',
            'tax_pension',
            'payout',
            [
                'attribute'=>'created_at',
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
                'label'=>'Created At',
                'value' => function ($data) {
                    return date('F j, Y',$data->created_at);
                },
                'options' => [ 'style'=>'width: 300px'],
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
                    'options' => [
                        'update' => 'payment.backend.default:' . AccessManager::UPDATE,
                        'delete' => 'payment.backend.default:' . AccessManager::DELETE
                    ],
                ]),
                'headerOptions' => ['class'=>'skip-export'],
                'contentOptions' => ['class'=>'skip-export'],
            ],
        ],
    ]); ?>
</div>
