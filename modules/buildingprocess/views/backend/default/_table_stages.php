<?php
use kartik\grid\GridView;
use modules\logposition\models\LogPosition;
use yii\widgets\Pjax;
use common\helpers\Toolbar;
use yii\helpers\Html;
use modules\buildingprocess\models\ProcessTemplate;

use common\access\AccessManager;

/* @var $this yii\web\View */

?>
<h3><?= Yii::t('app', 'Stages') ?></h3>
<?php Pjax::begin([
    'id' => 'stages',
    'enablePushState' => false,
]) ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'tableOptions' => [
        'class' => 'sortable-table',
    ],
    'rowOptions' => function ($data) {
        return [ 'id' => $data->id ];
    },
    'resizeStorageKey' => 'moduleGrid',
    'panel' => [
        'footer' => '',
    ],
    'toolbar' => [
        Toolbar::refreshButton(),
    ],
    'columns' => [
        'title',
        [
            'class' => 'yii\grid\ActionColumn',
            'buttons' => [
                'update' => function ($url, $model) {

                    $options = [
                        'title' => Yii::t('app', 'Update'),
                        'class' => 'pjax-modal-stages',
                    ];
                    $iconClass = 'glyphicon-pencil';


                    return $model->title === ProcessTemplate::FINAL_DEAL  ? '' : Html::a('<span class="glyphicon ' . $iconClass . '"></span>',
                        ['stages-update', 'id' => $model->id], $options);
                },
                'delete' => function ($url, $model) {

                    $options = [
                        'title' => Yii::t('app', 'Delete'),
                        'class' => 'pjax-modal-stages',
                    ];
                    $iconClass = 'glyphicon-trash';

                    return $model->title === ProcessTemplate::FINAL_DEAL  ? '' : Html::a('<span class="glyphicon ' . $iconClass . '"></span>',
                        ['stages-delete', 'id' => $model->id], $options);
                },
            ],
            'template' => $this->render('@backend/views/layouts/_options', [
                'options' => [
                    'update' => 'buildingprocess.backend.deal:' . AccessManager::UPDATE,
                    'delete' => 'buildingprocess.backend.deal:' . AccessManager::DELETE
                ],
            ]),
            'headerOptions' => ['class' => 'skip-export'],
            'contentOptions' => ['class' => 'skip-export'],
        ],
    ],
]); ?>


<?php Pjax::end() ?>
