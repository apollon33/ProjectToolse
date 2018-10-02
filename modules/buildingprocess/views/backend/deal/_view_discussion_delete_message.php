<?php
use modules\buildingprocess\models\Discussion;
use yii\helpers\Html;

/* @var $userId  integer user owner id */
?>

<div class="content js-content">

    <?php if ($messagesArray): ?>
    <?php foreach ($messagesArray as $key => $messages):?>
            <?php if (isset($messages['counter'])):?>
                <?php continue; ?>
            <?php endif;?>
        <div class=" discussion-container-common" style="background-color:<?= $messages['color'] ?>">
            <?php if ($messages['userId'] == $userId && ($messages['addTime'] + Discussion::DELETE_BUTTON_VISIBILITY_TIME ) > time()):?>
                <?= Html::a(Yii::t('app', 'Delete'), '#',['class' => 'js-delete-discussion-message' ,'id'=> $messages['id']]) ?>
            <?php endif;  ?>
            <p><?= $messages['name']; ?> wrote on <?= $messages['day']; ?> <?= $messages['date']; ?> channel <?= $messages['channel'] ?></p>
            <div class="discussion-message">
                <p><?= $messages['text']; ?></p>
            </div>
        </div>
    <?php endforeach;?>
<?php endif;?>
</div>
