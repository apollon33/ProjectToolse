<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
?>

<div class="pull-left">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'project_id',
                'value' => !empty($model->project) ? $model->project->name : null,
            ],
            [
                'attribute' => 'user_id',
                'value' => !empty($model->user) ? (!empty($model->user->fullName) ? $model->user->fullName : null) : null,
            ],
            [
                'attribute' => 'created_by',
                'value' => !empty($model->createdBy) ? (!empty($model->createdBy->fullName) ? $model->createdBy->fullName : null) : null,
            ],
            'created',
            'updated',
        ],
    ]) ?>
</div>

<div class="pull-left">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'estimated_time',
                'format' => 'duration',
            ],
            [
                'attribute' => 'estimate_approval',
                'format' => 'boolean',
            ],
            'estimated_time',
            [
                'label' => 'Start time',
                'attribute' => 'start_at',
            ],
            [
                'label' => 'End time',
                'attribute' => 'end_at',
            ],
        ],
    ]) ?>
</div>

<div class="clear"></div>

<table class="table table-striped table-bordered detail-view">
    <tbody>
        <tr>
            <th><?= Yii::t('app', 'Description') ?></th>
        </tr>
        <tr>
            <td>
                <?php if (!empty($model->description)): ?>
                    <?= $model->description ?>
                <?php else: ?>
                    <span class="grey"><?= Yii::t('app', 'No description') ?></span>
                <?php endif; ?>
            </td>
        </tr>
    </tbody>
</table>

<br />

<div class="clear"></div>