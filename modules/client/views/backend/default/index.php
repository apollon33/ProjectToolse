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

$this->title = Yii::t('app', 'Clients');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
Modal::begin([
    'id' => 'campaign-modal-trash',
    'header' => '<h4 class="modal-title">'.Yii::t('app', 'Trash') .'</h4>',
    'toggleButton' => [
        'class' => 'campaign-button-trash hidden',
        'data-target' => '#campaign-modal-trash',
    ],
    'clientOptions' => false,
]); ?>
<?php Pjax::begin([
    'id' => 'formTrash',
    'enablePushState' => false,
    'linkSelector' => 'a.pjax-modal-trash'
]); ?>

<?php Pjax::end(); ?>


<?php Modal::end(); ?>

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
        'panel' => [
            'footer' => Html::tag('div', Toolbar::createButton(Yii::t('app', 'Add Client')), ['class' => 'pull-left'])
                . Toolbar::paginationSelect($dataProvider),
        ],
        'toolbar' => [
            Toolbar::toggleButton($dataProvider),
            Toolbar::refreshButton(),
            Toolbar::createButton(Yii::t('app', 'Add Client')),
            Toolbar::deleteButton(),
            //Toolbar::showSelect(),
            Toolbar::exportButton(),
//            (!Yii::$app->user->isGuest ? (Yii::$app->user->identity->isAdmin() ? Toolbar::basketButton() : "") : ""),
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
                    'delete' => function ($url, $model) {
                        $expandButton = Html::tag('span', '', ['class' => 'glyphicon glyphicon-trash']);
                        return Html::a($expandButton, ['archive', 'id' => $model->id], [
                            'title' => Yii::t('app', 'Trash'),
                            'aria-label' => Yii::t('app', 'Trash'),
                        ]);
                    },


                ],
                'template' => $this->render('@backend/views/layouts/_options', [
                    'options' => [
                        'view' => 'client.backend.default:' . AccessManager::VIEW,
                        'update' => 'client.backend.default:' . AccessManager::UPDATE,
                        'delete' => 'client.backend.default:' . AccessManager::DELETE
                    ],
                ]),
                'headerOptions' => ['class'=>'skip-export'],
                'contentOptions' => ['class'=>'skip-export'],
            ],
        ],
    ]); ?>
    <?php Pjax::end();?>
</div>
