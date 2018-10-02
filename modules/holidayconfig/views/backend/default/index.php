<?php

use modules\holidayconfig\models\HolidayConfig;
use yii\helpers\Html;
use kartik\grid\GridView;
use common\helpers\Toolbar;
use yii\jui\DatePicker;

use common\access\AccessManager;

/* @var $this yii\web\View */
/* @var $searchModel modules\holidayconfig\models\HolidayConfigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Holiday Configs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="holiday-config-index">

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
        'resizeStorageKey' => 'holiday-configGrid',
        'panel' => [
            'footer' => Html::tag('div', Toolbar::createButton(Yii::t('app', 'Item')), ['class' => 'pull-left'])
                . Toolbar::paginationSelect($dataProvider),
        ],
        'toolbar' => [
            Toolbar::toggleButton($dataProvider),
            Toolbar::refreshButton(),
            Toolbar::createButton(Yii::t('app', 'Add Item')),
            Toolbar::deleteButton(),
            //Toolbar::showSelect(),
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
                'attribute' => 'month',
                'filter' => Toolbar::month(),
                'value' => function ($data) {
                    return Toolbar::viewMonth($data->month);
                },
            ],
            [
                'attribute' => 'day',
                'filter' => Toolbar::listRandom(range(0, 31)),
            ],
            'name',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => $this->render('@backend/views/layouts/_options', [
                    'options' => [
                        'update' => 'holidayconfig.backend.default:' . AccessManager::UPDATE,
                        'delete' => 'holidayconfig.backend.default:' . AccessManager::DELETE
                    ],
                ]),
                'headerOptions' => ['class'=>'skip-export'],
                'contentOptions' => ['class'=>'skip-export'],
            ],
        ],
    ]); ?>
</div>
