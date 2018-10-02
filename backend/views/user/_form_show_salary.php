<?php
use kartik\grid\GridView;
use modules\logsalary\models\LogSalary;
use yii\widgets\Pjax;
use yii\helpers\Html;

use common\access\AccessManager;

/* @var $this yii\web\View */
/* @var $searchModel modules\user\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<?php Pjax::begin(['id' => 'notes-salary']) ?>
<?= GridView::widget([
    'dataProvider' => $dataProviderSalary,
    'columns' => [
        [
            'label'=>'Date',
            'value' => function ($data) {
                return Yii::$app->formatter->asDate($data->created_at);
            },
            'contentOptions'=>['style'=>'width: 150px;'],
        ],
        [
            'attribute'=>'salary',
            'label'=>'Salary',
            'contentOptions'=>['style'=>'width: 100px;'],
        ],
        [
            'label'=>'Currency',
            'value' => function ($data) {
                return LogSalary::getCurrency()[$data->currency];
            },
            'contentOptions'=>['style'=>'width: 100px;'],
        ],
        [
            'attribute'=>'bonus',
            'label'=>'bonus',
            'contentOptions'=>['style'=>'width: 100px;'],
        ],
        [
            'label'=>'bonus_currency',
            'value' => function ($data) {
                return LogSalary::getCurrency()[$data->bonus_currency];
            },
            'contentOptions'=>['style'=>'width: 100px;'],
        ],
        [
            'attribute'=>'reporting_salary',
            'label'=>'Reporting Salary',
            'value' => function ($data) {
                return $data->reporting_salary.' ₴ UAH';
            },
            'contentOptions'=>['style'=>'width: 100px;'],
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
                        'class' => 'pjax-modal-salary',
                    ];
                    $iconClass = 'glyphicon-trash';

                    return Html::a('<span class="glyphicon ' . $iconClass . '"></span>',
                        ['logsalary/delete', 'id' => $model->id], $options);
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
