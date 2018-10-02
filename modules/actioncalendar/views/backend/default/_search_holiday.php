<?php

use kartik\datecontrol\DateControl;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use yii\helpers\Url;
use common\helpers\Toolbar;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel modules\holiday\models\HolidaySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


?>
<div class="actioncalendar-index">

    <h3><?= Yii::t('app', 'Holidays') ?></h3>
    <?php Pjax::begin([
        'id' => 'holidayList',
        'enablePushState' => false,
        'enableReplaceState' => false,
    ]) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions'=>[
            'class'=>'table-striped table-bordered table-condensed ',
            'style' => 'b: center;'
        ],
        'options'=>['style' => 'text-align: center;'],
        'resizeStorageKey' => 'holidayGrid',
        'columns' => [
            [
                'attribute'=>'date',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
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
            ],
            [
                'attribute'=>'name',
                'headerOptions' => [
                    'class' => 'text-center',
                ],
                'label'=>Yii::t('app', 'Name holiday'),
                'value' =>'name',
            ],

        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
