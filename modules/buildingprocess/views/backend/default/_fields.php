<?php
use kartik\grid\GridView;
use modules\logposition\models\LogPosition;
use yii\widgets\Pjax;
use common\helpers\Toolbar;
use yii\helpers\Html;

use common\access\AccessManager;

/* @var $this yii\web\View */

?>
<?php Pjax::begin([
    'id' => 'field',
    'enablePushState' => false,
]) ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'type_field',
        'name',
        'validation',
        [

            'class' => 'yii\grid\ActionColumn',
            'buttons' => [
                'update' => function ($url, $model) {

                    $options = [
                        'title' => Yii::t('app', 'Update'),
                        'class' => 'pjax-modal-stages'
                    ];
                    $iconClass = 'glyphicon-pencil';
                    return Html::a('<span class="glyphicon ' . $iconClass . '"></span>', ['stages-update',  'id' => $model->id ], $options);
                },
                'delete' => function ($url, $model) {

                    $options = [
                        'title' => Yii::t('app', 'Delete'),
                        'class' => 'pjax-modal-stages'
                    ];
                    $iconClass = 'glyphicon-trash';
                    return Html::a('<span class="glyphicon ' . $iconClass . '"></span>', ['stages-delete',  'id' => $model->id ], $options);
                },
            ],
            'template' => $this->render('@backend/views/layouts/_options', [
                'options' => [
                    'update' => 'buildingprocess.backend.deal:' . AccessManager::UPDATE,
                    'delete' => 'buildingprocess.backend.deal:' . AccessManager::DELETE
                ],
                'options' => ['update', 'delete'],
            ]),
            'headerOptions' => ['class'=>'skip-export'],
            'contentOptions' => ['class'=>'skip-export'],
        ],
    ],
]); ?>

<?php Pjax::end() ?>
