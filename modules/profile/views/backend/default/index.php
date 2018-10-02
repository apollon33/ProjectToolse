<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\helpers\Toolbar;
use kartik\money\MaskMoney;
use kartik\datecontrol\DateControl;

use common\access\AccessManager;

/* @var $this yii\web\View */
/* @var $searchModel modules\profile\models\ProfileSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Profiles');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profile-index">

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
        'resizeStorageKey' => 'profileGrid',
        'panel' => [
            'footer' => Html::tag('div', Toolbar::createButton(Yii::t('app', 'Add Profile')), ['class' => 'pull-left'])
                . Toolbar::paginationSelect($dataProvider),
        ],
        'toolbar' => [
            Toolbar::toggleButton($dataProvider),
            Toolbar::refreshButton(),
            Toolbar::createButton(Yii::t('app', 'Add Profile')),
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
            'name',
            'fullName',
            [
                'attribute' => 'rate',
                'value' => function ($data) {
                    return '$ '.$data->rate;
                },
                'filter' => Html::tag(
                    'div',
                    MaskMoney::widget([
                            'class' => 'form-control',
                            'model' => $searchModel,
                            'attribute' => 'rate_from',
                            'options' => [
                                'layout' => '{remove}{input}',
                                'options' => ['placeholder' => Yii::t('app', 'From rate'),],
                                'style'=>'width: 100px',
                            ],
                            'pluginOptions' => [
                                'prefix' => '$ ',
                                'allowNegative' => false,
                            ],
                    ]).
                    MaskMoney::widget([
                        'class' => 'form-control',
                        'model' => $searchModel,
                        'attribute' => 'rate_to',
                        'options' => [
                            'layout' => '{remove}{input}',
                            'options' => ['placeholder' => Yii::t('app', 'To rate'),],
                            'style'=>'width: 100px',
                        ],
                        'pluginOptions' => [
                            'prefix' => '$ ',
                            'allowNegative' => false,
                        ],
                    ]),
                    //Html::activeInput('text', $searchModel, 'rate_from',['class' => 'form-control','placeholder'=>'from','pluginOptions' => ['prefix' => '$ ', 'allowNegative' => false]]).
                    //Html::activeInput('text', $searchModel, 'rate_to',['class' => 'form-control','placeholder'=>'to']),
                    ['class' => 'form-inline']
                ),
                'options' => [ 'style'=>'width: 300px'],
            ],


            [
                'class' => 'yii\grid\ActionColumn',
                'template' => $this->render('@backend/views/layouts/_options', [
                    'options' => [
                        'update' => 'profile.backend.default:' . AccessManager::UPDATE,
                        'delete' => 'profile.backend.default:' . AccessManager::DELETE,
                    ],
                ]),
                'headerOptions' => ['class'=>'skip-export'],
                'contentOptions' => ['class'=>'skip-export'],
            ],

        ],
    ]); ?>
</div>
