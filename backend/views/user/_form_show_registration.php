<?php
use kartik\grid\GridView;
use modules\logregistration\models\LogRegistration;
use yii\widgets\Pjax;
use yii\helpers\Html;

use common\access\AccessManager;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<?php Pjax::begin(['id' => 'notes-registration']) ?>
<?= GridView::widget([
    'dataProvider' => $dataProviderRegistration,
    'columns' => [
        [
            'label'=>'Date',
            'value' => function ($data) {
                return Yii::$app->formatter->asDate($data->created_at);
            },
            'contentOptions'=>['style'=>'width: 150px;'],
        ],
        [
            'label'=>'Registration',
            'value' => function ($data) {
                return $data->registration->name;
            },
            'contentOptions'=>['style'=>'width: 200px;'],
        ],
        [
            'label'=>'Description',
            'value' => 'description'
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'buttons' => [
                'delete' => function ($url, $model) {

                    $options = [
                        'title' => Yii::t('app', 'Delete'),
                        'class' => 'pjax-modal-registration',
                    ];
                    $iconClass = 'glyphicon-trash';

                    return Html::a('<span class="glyphicon ' . $iconClass . '"></span>',
                        ['logregistration/delete', 'id' => $model->id], $options);
                },
            ],
            'template' => $this->render('@backend/views/layouts/_options', [
                'options' => [
                    'delete' => 'core.backend.admin:' . AccessManager::DELETE,
                ],
            ]),
            'headerOptions' => ['class' => 'skip-export'],
            'contentOptions' => ['class' => 'skip-export'],
        ],


    ],
]); ?>

<?php Pjax::end() ?>
