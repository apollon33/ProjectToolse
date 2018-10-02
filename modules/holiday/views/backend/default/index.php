<?php

use kartik\datecontrol\DateControl;
use yii\helpers\Html;
use kartik\grid\GridView;
use common\helpers\Toolbar;
use yii\widgets\ActiveForm;

use common\access\AccessManager;

/* @var $this yii\web\View */
/* @var $searchModel modules\holiday\models\HolidaySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Holidays');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="holiday-index">

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
        'resizeStorageKey' => 'holidayGrid',
        'panel' => [
            'footer' => Html::tag('div', Toolbar::createButton(Yii::t('app', 'Add Item')), ['class' => 'pull-left'])
                . Toolbar::paginationSelect($dataProvider),
        ],
        'toolbar' => [
            Toolbar::toggleButton($dataProvider),
            Toolbar::refreshButton(),
            Toolbar::createButton(Yii::t('app', 'Add Item')),
            Toolbar::deleteButton(),
            Toolbar::exportButton(),
            Toolbar::putdownButton(),
        ],
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'headerOptions' => ['class'=>'skip-export'],
                'contentOptions' => ['class'=>'skip-export'],
            ],
            'id',
            [
                'attribute'=>'date',
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
                'label'=>Yii::t('app', 'Day holiday'),
                'value' => function ($data) {
                    return date('F j, Y',$data->date);
                },
            ],[
                'attribute'=>'name',
                'label'=>Yii::t('app', 'Name holiday'),
                'value' =>'name',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => $this->render('@backend/views/layouts/_options', [
                    'options' => [
                        'update' => 'holiday.backend.default:' . AccessManager::UPDATE,
                        'delete' => 'holiday.backend.default:' . AccessManager::DELETE
                    ],
                ]),
                'headerOptions' => ['class'=>'skip-export'],
                'contentOptions' => ['class'=>'skip-export'],
            ],
        ],
    ]); ?>
</div>
