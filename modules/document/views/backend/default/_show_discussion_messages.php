<?php
use modules\buildingprocess\models\Discussion;
use yii\helpers\Html;

/* @var $userId  integer user owner id */
?>

<div class="content js-content">

    <?php if (isset($messages)): ?>
        <?php foreach ($messages as $key => $message):?>
            <?php if (isset($message['counter'])):?>
                <?php continue; ?>
            <?php endif;?>
            <div class=" discussion-container-common" style="background-color:<?= $message['color'] ?>">
                <?php if ($message['userId'] == $userId && ($message['addTime'] + Discussion::DELETE_BUTTON_VISIBILITY_TIME ) > time()):?>
                    <?= Html::a(Yii::t('app', 'Delete'), '#',['class' => 'js-delete-discussion-message' ,'id'=> $message['id']]) ?>
                <?php endif;  ?>
                <p><?= $message['name']; ?> wrote on <?= $message['day']; ?> <?= $message['date']; ?> channel <?= $message['channel']; ?> </p>
                <div class="discussion-message">
                    <p><?= $message['text']; ?></p>
                </div>
            </div>
        <?php endforeach;?>
    <?php endif;?>
</div>
