<?php

use kartik\datecontrol\DateControl;
use common\models\User;
use yii\helpers\Html;
use modules\vacation\models\Vacation;
use kartik\grid\GridView;
use common\helpers\Toolbar;

use common\access\AccessManager;

/* @var $this yii\web\View */
/* @var $searchModel modules\vacation\models\VacationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Vacations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vacation-index">

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
        'resizeStorageKey' => 'vacationGrid',
        'panel' => [
            'footer' => Html::tag('div', Toolbar::createButton(Yii::t('app', 'Add Vacation')), ['class' => 'pull-left'])
                . Toolbar::paginationSelect($dataProvider),
        ],
        'toolbar' => [
            Toolbar::toggleButton($dataProvider),
            Toolbar::refreshButton(),
            Toolbar::createButton(Yii::t('app', 'Add Vacation')),
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
                'attribute' => 'user_id',
                'filter' => User::getList(),
                'value' =>'user.fullName',
            ],
            [
                'attribute'=>'StartAt',
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
                            'options' => ['placeholder' => Yii::t('app', 'From date')],
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
                            'options' => ['placeholder' => Yii::t('app', 'To date')],
                        ],
                    ]),
                    ['class' => 'date-range']
                ),
                'label'=>'Start',
                'options' => [ 'style'=>'width: 300px'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => $this->render('@backend/views/layouts/_options', [
                    'options' => [
                        'update' => 'vacation.backend.default:' . AccessManager::UPDATE,
                        'delete' => 'vacation.backend.default:' . AccessManager::DELETE
                    ],
                ]),
                'headerOptions' => ['class' => 'skip-export'],
                'contentOptions' => ['class' => 'skip-export'],
            ],
        ],
    ]); ?>
</div>
