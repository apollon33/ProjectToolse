<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\helpers\Toolbar;
use kartik\money\MaskMoney;
use modules\project\models\Project;
use modules\client\models\Client;
use modules\profile\models\Profile;

use common\access\AccessManager;

/* @var $this yii\web\View */
/* @var $searchModel modules\project\models\PeojectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Projects');
$this->params['breadcrumbs'][] = $this->title;
$this->title = Yii::t('app', 'Projects');
?>
<div class="project-index">

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
        'resizeStorageKey' => 'projectGrid',
        'panel' => [
            'footer' => Html::tag('div', Toolbar::createButton(Yii::t('app', 'Add Project')), ['class' => 'pull-left'])
                . Toolbar::paginationSelect($dataProvider),
        ],
        'toolbar' => [
            Toolbar::toggleButton($dataProvider),
            Toolbar::refreshButton(),
            Toolbar::createButton(Yii::t('app', 'Add Project')),
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
                'attribute'=>'name',
                'value' => 'name',
            ],
            [
                'attribute'=>'client_id',
                'filter' => Client::getList(),
                'value' => 'client.FullName'
            ],
            [
                'attribute'=>'profile_id',
                'filter' => Profile::getList(),
                'value' => 'profile.FullName'
            ],
            [
                'label'=>'Rate',
                'attribute' => 'rate',
                'options' => ['style'=>'width: 240px'],
                'value' => function ($data) {
                    return '$ '.$data->rate;
                },
                'filter' => Html::tag(
                    'div',
                    MaskMoney::widget([
                        'class' => 'form-control',
                        'model' => $searchModel,
                        'attribute' => 'salary_from',
                        'options' => [
                            'layout' => '{remove}{input}',
                            'options' => ['placeholder' => Yii::t('app', 'From salary')],
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
                        'attribute' => 'salary_to',
                        'options' => [
                            'layout' => '{remove}{input}',
                            'options' => ['placeholder' => Yii::t('app', 'To salary')],
                            'style'=>'width: 100px',
                        ],
                        'pluginOptions' => [
                            'prefix' => '$ ',
                            'allowNegative' => false,
                        ],
                    ]),
                    ['class' => 'form-inline']
                ),
                'options' => [ 'style'=>'width: 300px',],
            ],
            // 'description:ntext',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => $this->render('@backend/views/layouts/_options', [
                    'options' => [
                        'update' => 'project.backend.default:' . AccessManager::UPDATE,
                        'delete' => 'project.backend.default:' . AccessManager::DELETE
                    ],
                ]),
                'headerOptions' => ['class'=>'skip-export'],
                'contentOptions' => ['class'=>'skip-export'],
            ],
        ],
    ]); ?>
</div>
