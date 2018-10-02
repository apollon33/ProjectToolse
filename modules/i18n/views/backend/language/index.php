<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\helpers\Toolbar;

use common\access\AccessManager;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Languages');
?>
<div class="language-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'resizeStorageKey' => 'languageGrid',
        'panel' => [
            'footer' => Toolbar::createButton(Yii::t('app', 'Add Language'))
        ],
        'toolbar' => [
            Toolbar::toggleButton($dataProvider),
            Toolbar::refreshButton(),
            Toolbar::createButton(Yii::t('app', 'Add Language')),
            Toolbar::deleteButton(),
        ],
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
            ],
            'id',
            [
                'attribute' => 'language',
                'format' => 'html',
                'value' => function ($data) {
                    return Html::tag('div', '', ['class' => 'flag flag-' . $data->language]);
                },
            ],
            'name:ntext',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => $this->render('@backend/views/layouts/_options', [
                    'options' => [
                        'update' => 'core.backend.user:' . AccessManager::UPDATE,
                        'delete' => 'core.backend.user:' . AccessManager::DELETE
                    ],
                ]),
            ],
        ],
    ]); ?>

</div>
