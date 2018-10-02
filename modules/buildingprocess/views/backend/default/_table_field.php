<?php
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Html;
use \modules\field\models\ProcessFieldTemplate;

use common\access\AccessManager;

/* @var $this yii\web\View */

?>
<h3><?= Yii::t('app', 'Fields') ?></h3>
<?php Pjax::begin([
    'id' => 'field',
    'enablePushState' => false,
]) ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'type_field',
            'value' => 'field'
        ],
        'name',
        [
            'class' => 'common\components\Column\SetColumn',
            'attribute' => 'required',
            'filter' => ProcessFieldTemplate::getRequired(),
            'cssClasses' => [
                ProcessFieldTemplate::REQUIRED_YES => 'success',
                ProcessFieldTemplate::REQUIRED_NO => 'danger',
            ],
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'buttons' => [
                'update' => function ($url, $model) {

                    $options = [
                        'title' => Yii::t('app', 'Update'),
                        'class' => 'pjax-modal-field'
                    ];
                    $iconClass = 'glyphicon-pencil';
                    return $model->name ===  ProcessFieldTemplate::DOCUMENT_DEAL || $model->name ===  ProcessFieldTemplate::NAME_DEAL || $model->name === ProcessFieldTemplate::DISCUSSION || $model->type_field === ProcessFieldTemplate::CLIENT_FIELD ? '' : Html::a('<span class="glyphicon ' . $iconClass . '"></span>', ['/field/field-update',  'id' => $model->id ], $options) ;
                },
                'delete' => function ($url, $model) {

                    $options = [
                        'title' => Yii::t('app', 'Delete'),
                        'class' => 'pjax-modal-field'
                    ];
                    $iconClass = 'glyphicon-trash';
                    return $model->name ===  ProcessFieldTemplate::DOCUMENT_DEAL || $model->name === ProcessFieldTemplate::NAME_DEAL || $model->name === ProcessFieldTemplate::DISCUSSION || $model->type_field === ProcessFieldTemplate::CLIENT_FIELD ? '' : Html::a('<span class="glyphicon ' . $iconClass . '"></span>', ['/field/field-delete',  'id' => $model->id ], $options);
                },
            ],
            'template' => $this->render('@backend/views/layouts/_options', [
                'options' => [
                    'update' => 'buildingprocess.backend.deal:' . AccessManager::UPDATE,
                    'delete' => 'buildingprocess.backend.deal:' . AccessManager::DELETE
                ],
            ]),
            'headerOptions' => ['class'=>'skip-export'],
            'contentOptions' => ['class'=>'skip-export'],
        ],
    ],
]); ?>

<?php Pjax::end() ?>
