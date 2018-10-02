<?php

use common\widgets\Gallery;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\activity\models\Activity */

$this->title = Yii::t('app', 'Activity') . ': ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Activities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= Gallery::widget() ?>

<div class="activity-view col-lg-8">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php if(!empty($model->screenshot)): ?>
        <p>
            <div class="row">
                <div class="pull-left col-lg-8">
                    <a href="<?= Url::to(['/activity/default/image-view', 'id' => $model->id]) ?>" title="<?= $model->target_window ?>" data-gallery>
                        <?= Html::img(Url::to(['/activity/default/image-view', 'id' => $model->id, 'thumbnail' => true]), ['class'=>'img-thumbnail']) ?><br />
                    </a>
                </div>
            </div>
        </p>
    <?php endif; ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            //'user_id',
            [
                'label' => Yii::t('app', 'User'),
                'value' => !empty($model->user) ? $model->user->getFullName() : null,
            ],
            //'project_id',
            [
                'label' => Yii::t('app', 'Project'),
                'value' => !empty($model->project) ? $model->project->name : null,
            ],
            'description',
            'target_window',
            //'keyboard_activity_percent',
            [
                'label' => Yii::t('app', 'Keyboard Activity'),
                'format' => 'raw',
                'value' => $this->render('_progress', ['progress' => $model->keyboard_activity_percent]),
                'inputContainer' => ['class'=>'col-sm-6'],
            ],
            [
                'label' => Yii::t('app', 'Mouse Activity'),
                'format' => 'raw',
                'value' => $this->render('_progress', ['progress' => $model->mouse_activity_percent]),
                'inputContainer' => ['class'=>'col-sm-6'],
            ],
            //'mouse_activity_percent',
            'interval:duration',
            'created',
            'updated',
        ],
    ]) ?>

</div>
