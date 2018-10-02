<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\MultipleEMails;

use common\access\AccessManager;

/* @var $this yii\web\View */
/* @var $searchModel common\models\MultipleEMailsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="multiple-emails-index">


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'resizeStorageKey' => 'multiple-emailsGrid',
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'headerOptions' => ['class'=>'skip-export'],
                'contentOptions' => ['class'=>'skip-export'],
            ],
            'email:email',
            'TypeEmail',
            [
                'class' => 'common\components\Column\SetColumn',
                'attribute' => 'active',
                'filter' => MultipleEMails::getActiveStatuses(),
                'cssClasses' => [
                    MultipleEMails::ACTIVE_YES => 'success',
                    MultipleEMails::ACTIVE_NO => 'danger',
                ],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'update' => function ($url, $model) {

                        $options = [
                            'title' => Yii::t('app', 'Update'),
                            'class' => 'pjax-modal-multiEmail',
                        ];
                        $iconClass = 'glyphicon-pencil';

                        return Html::a('<span class="glyphicon ' . $iconClass . '"></span>',
                            ['update-multiple-emails', 'id' => $model->id], $options);
                    },
                    'delete' => function ($url, $model) {

                        $options = [
                            'title' => Yii::t('app', 'Delete'),
                            'class' => 'pjax-modal-multiEmail',
                        ];
                        $iconClass = 'glyphicon-trash';

                        return Html::a('<span class="glyphicon ' . $iconClass . '"></span>',
                            ['delete-multiple-emails', 'id' => $model->id], $options);
                    },
                ],
                'template' => $this->render('@backend/views/layouts/_options', [
                    'options' => [
                        'update' => 'core.backend.user:' . AccessManager::MANAGE,
                        'delete' => 'core.backend.user:' . AccessManager::MANAGE
                    ],
                ]),
                'headerOptions' => ['class'=>'skip-export'],
                'contentOptions' => ['class'=>'skip-export'],
            ],
        ],
    ]); ?>
</div>
