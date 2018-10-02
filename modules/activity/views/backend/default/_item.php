<?php
use yii\helpers\Html;
use common\components\DurationConverter;
use yii\helpers\Url;
?>

<?php global $exHours; ?>
<?php $hours = Yii::$app->formatter->asDatetime($model->created_at, 'h'); ?>
<?php $meridiem = Yii::$app->formatter->asDatetime($model->created_at, 'a'); ?>

<?php if (empty($exHours) || $exHours != $hours): ?>
    </div>
    <div class="clearfix"></div>
    <div class="time-block col-md-1">
        <?= $hours ?> <?= $meridiem ?>
    </div>
    <div class="col-md-11">
<?php endif; ?>

<div class="activity-block">

    <div class="row">
        <div class="col-md-10">
            <div class="pull-left"><b><?= Yii::$app->formatter->asDatetime($model->created_at, 'HH:mm'); ?></b></div>
            <div class="pull-right"><b><?= DurationConverter::shortDuration(Yii::$app->formatter->asDuration($model->interval)) ?></b></div>
        </div>
    </div>

    <a href="<?= Url::to(['/activity/default/image-view', 'id' => $model->id]) ?>" title="<?= $model->target_window ?>" data-gallery>
        <?= Html::img(Url::to(['/activity/default/image-view', 'id' => $model->id, 'thumbnail' => true]), ['class' => 'img-thumbnail']) ?><br />
    </a>

    <?php /*if ($model->project): ?>
        <b><?= Yii::t('app', 'Project') ?>:</b>
        <?= $model->project->name ?><br />
    <?php endif; ?>

    <?php if ($model->target_window): ?>
        <b><?= Yii::t('app', 'Target Window') ?>:</b>
        <?= $model->target_window ?><br />
    <?php endif; ?>

    <?php if ($model->interval): ?>
        <b><?= Yii::t('app', 'Interval') ?>:</b>
        <?= Yii::$app->formatter->asDuration($model->interval) ?><br />
    <?php endif;*/ ?>

    <div class="row">
        <div class="col-md-5">
            <?= Yii::t('app', 'Keyboard') ?>:
            <?= $this->render('_progress', ['progress' => $model->keyboard_activity_percent]); ?>
        </div>
        <div class="col-md-5">
            <?= Yii::t('app', 'Mouse') ?>:
            <?= $this->render('_progress', ['progress' => $model->mouse_activity_percent]); ?>
        </div>
    </div>

    <?php /*<b><?= Yii::t('app', 'Description') ?>:</b>
    <?= $model->description ?><br />

    <b><?= Yii::t('app', 'Created') ?>:</b>
    <?= $model->created*/ ?>

</div>

<?php $exHours = $hours; ?>

