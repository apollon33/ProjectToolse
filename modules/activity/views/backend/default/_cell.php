<?php
use yii\helpers\Url;
?>
<div class="day-statistics">
    <a href="<?= Url::to(['/activity/report', 'ActivitySearch' => ['user_id' => $userId, 'date' => $data['date']]]) ?>">
        <b><?= $data['interval'] ?></b>
        <div>
            <?= Yii::t('app', 'Keyboard') ?>
            <?= $this->render('_progress', ['progress' => $data['keyboard_activity_percent']]); ?>
            <?= Yii::t('app', 'Mouse') ?>
            <?= $this->render('_progress', ['progress' => $data['mouse_activity_percent']]); ?>
        </div>
    </a>
</div>