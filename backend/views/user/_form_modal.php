<?php

use yii\bootstrap\Modal;
use yii\widgets\Pjax;


?>
<?php
Modal::begin([
    'id' => 'campaign-modal',
    'header' => '<h4 class="modal-title">'.Yii::t('app', 'Position') .'</h4>',
    'toggleButton' => [
        'class' => 'campaign-button hidden',
        'data-target' => '#campaign-modal',
    ],
    'clientOptions' => false,
]); ?>
<?php Pjax::begin([
    'id' => 'formCampaign',
    'enablePushState' => false,
    'linkSelector' => 'a.pjax-modal-position'
]); ?>

<?php Pjax::end(); ?>


<?php Modal::end(); ?>




<?php
Modal::begin([
    'id' => 'campaign-modal-registration',
    'header' => '<h4 class="modal-title">'.Yii::t('app', 'Registration') .'</h4>',
    'toggleButton' => [
        'class' => 'campaign-button-registration hidden',
        'data-target' => '#campaign-modal-registration',
    ],
    'clientOptions' => false,
]); ?>
<?php Pjax::begin([
    'id' => 'formCampaign-registration',
    'enablePushState' => false,
    'linkSelector' => 'a.pjax-modal-registration'
]); ?>

<?php Pjax::end(); ?>


<?php Modal::end(); ?>


<?php
Modal::begin([
    'id' => 'campaign-modal-salary',
    'header' => '<h4 class="modal-title">'.Yii::t('app', 'Salary') .'</h4>',
    'toggleButton' => [
        'class' => 'campaign-button-salary hidden',
        'data-target' => '#campaign-modal-salary',
    ],
    'clientOptions' => false,
]); ?>
<?php Pjax::begin([
    'id' => 'formCampaign-salary',
    'enablePushState' => false,
    'linkSelector' => 'a.pjax-modal-salary'
]); ?>

<?php Pjax::end(); ?>


<?php Modal::end(); ?>

<?php
Modal::begin([
    'id' => 'campaign-modal-vacation',
    'header' => '<h4 class="modal-title">'.Yii::t('app', 'Vacation') .'</h4>',
    'toggleButton' => [
        'class' => 'campaign-button-vacation hidden',
        'data-target' => '#campaign-modal-vacation',
    ],
    'clientOptions' => false,
]); ?>
<?php Pjax::begin([
    'id' => 'formCampaign-vacation',
    'enablePushState' => false,
    'linkSelector' => 'a.pjax-modal-vacation'
]); ?>

<?php Pjax::end(); ?>


<?php Modal::end(); ?>

<?php
Modal::begin([
    'id' => 'multiEmail-modal',
    'header' => '<h4 class="modal-title">'.Yii::t('app', 'Email') .'</h4>',
    'toggleButton' => [
        'class' => 'multiEmail-button hidden',
        'data-target' => '#multiEmail-modal',
    ],
    'clientOptions' => false,
]); ?>
<?php Pjax::begin([
    'id' => 'formMultiEmail',
    'enablePushState' => false,
    'linkSelector' => 'a.pjax-modal-multiEmail'
]); ?>

<?php Pjax::end(); ?>


<?php Modal::end(); ?>
