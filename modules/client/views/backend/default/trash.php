<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use common\helpers\Toolbar;
use modules\client\models\Client;
use common\access\AccessManager;

/* @var $this yii\web\View */
/* @var $searchModel modules\client\models\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Trash Clients');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="client-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php Pjax::begin(['id' => 'trash']) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        /*'tableOptions' => [
            'class' => 'sortable-table',
        ],
        'rowOptions' => function ($data) {
            return ['id' => $data->id];
        },*/
        'resizeStorageKey' => 'clientGrid',
        'toolbar' => [
            Toolbar::toggleButton($dataProvider),
            Toolbar::refreshButton(),
            Toolbar::deleteButton(),
        ],        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'headerOptions' => ['class'=>'skip-export'],
                'contentOptions' => ['class'=>'skip-export'],
            ],
            'id',
            'client_name',
            'email:email',
            'skype',
            'phone',
            // 'country_id',
            // 'address',
            // 'description',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'update' => function ($url, $model) {
                        $expandButton = Html::tag('span', '', ['class' => 'glyphicon glyphicon-repeat']);
                        return Html::a($expandButton, ['restore', 'id' => $model->id], [
                            'title' => Yii::t('app', 'Restore'),
                            'aria-label' => Yii::t('app', 'Restore'),
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        $expandButton = Html::tag('span', '', ['class' => 'glyphicon glyphicon-trash']);
                        return Html::a($expandButton, ['delete', 'id' => $model->id], [
                            'title' => Yii::t('app', 'Delete'),
                            'aria-label' => Yii::t('app', 'Delete'),
                        ]);
                    }
                ],
                'template' => $this->render('@backend/views/layouts/_options', [
                    'options' => [
                        'update' => 'client.backend.default:' . AccessManager::UPDATE,
                        'delete' => 'client.backend.admin:' . AccessManager::DELETE,
                    ],
                ]),
                'headerOptions' => ['class'=>'skip-export'],
                'contentOptions' => ['class'=>'skip-export'],
            ],
        ],
    ]); ?>
</div>
