<?php

use common\widgets\Gallery;
use modules\activity\models\Activity;
use modules\activity\models\ActivitySearch;
use modules\project\models\Project;
use common\models\User;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use common\helpers\Toolbar;

/* @var $this yii\web\View */
/* @var $searchModel modules\activity\models\ActivitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Activities';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= Gallery::widget() ?>
<div class="activity-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        /*'tableOptions' => [
            'class' => 'sortable-table',
        ],
        'rowOptions' => function ($data) {
            return ['id' => $data->id];
        },*/
        'resizeStorageKey' => 'activityGrid',
        'panel' => [
            'footer' => Html::tag('div', Toolbar::createButton(Yii::t('app', 'Add Activity')), ['class' => 'pull-left'])
                . Toolbar::paginationSelect($dataProvider),
        ],
        'toolbar' => [
            Toolbar::toggleButton($dataProvider),
            Toolbar::refreshButton(),
            Toolbar::createButton(Yii::t('app', 'Add Activity')),
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
                'label' => Yii::t('app', 'User'),
                'attribute' => 'user_id',
                'filter' => User::getList(),
                'value' => function ($model) {
                    /** @var Activity $model */
                    return !empty($model->user) ? $model->user->getFullName() : null;
                }
            ],
            [
                'label' => Yii::t('app', 'Project'),
                'attribute' => 'project_id',
                'filter' => Project::getList(),
                'value' => function ($model) {
                    /** @var Activity $model */
                    return !empty($model->project) ? $model->project->name : null;
                }
            ],
            [
                'label' => Yii::t('app', 'Screenshot'),
                'attribute' => 'screen',
                'format' => 'raw',
                'filter' => ActivitySearch::getScreenStatuses(),
                'value' => function ($model) {
                    /** @var Activity $model */
                    if (!empty($model->screenshot)) {
                        return '<a href="' . Url::to(['/activity/default/image-view', 'id' => $model->id]) . '" title="' . $model->target_window .'" data-gallery>'
                        . Html::img(Url::to(['/activity/default/image-view', 'id' => $model->id, 'thumbnail' => true]), ['class' => 'img-preview', 'title' => $model->target_window, 'alt' => $model->target_window])
                        . '<br />'
                        . '</a>';
                    } else {
                        return null;
                    }
                },
                'headerOptions' => ['class' => 'skip-export'],
                'contentOptions' => ['class' => 'skip-export'],
            ],
            'target_window',
            [
                'label' => Yii::t('app', 'Keyboard Activity'),
                'attribute' => 'keyboard_activity_percent',
                'format' => 'html',
                'value' => function ($model) {
                    /** @var Activity $model */
                    return $this->render('_progress', ['progress' => $model->keyboard_activity_percent]);
                }
            ],
            [
                'label' => Yii::t('app', 'Mouse Activity'),
                'attribute' => 'mouse_activity_percent',
                'pageSummary' => true,
                'format' => 'html',
                'value' => function ($model) {
                    /** @var Activity $model */
                    return $this->render('_progress', ['progress' => $model->mouse_activity_percent]);
                }
            ],
            'interval:duration',
            'created',
            // 'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                /*'buttons' => [
                    'show' => function ($url, $model) {
                        if ($model->visible) {
                            $options = ['title' => Yii::t('app', 'Disable')];
                            $iconClass = 'glyphicon-unlock';
                        } else {
                            $options = ['title' => Yii::t('app', 'Enable')];
                            $iconClass = 'glyphicon-lock';
                        }
                        return Html::a('<span class="glyphicon ' . $iconClass . '"></span>', $url, $options);
                    },
                ],
                'template' => $this->render('@backend/views/layouts/_options', [
                    'options' => ['update', 'show', 'delete'],
                ]),*/
                'headerOptions' => ['class'=>'skip-export'],
                'contentOptions' => ['class'=>'skip-export'],
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
