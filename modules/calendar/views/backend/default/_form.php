<?php
use yii\helpers\Html;
use yii\jui\DatePicker;
use common\models\User;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use kartik\datetime\DateTimePicker;


$userAgeLimit  = 100;
$datePickerRange = (date('Y') - $userAgeLimit) . ':' . date('Y');
?>
<?php
Modal::begin([
    'id' => 'calendar-modal',
    'header' => '<h4 class="modal-title">'.Yii::t('app', 'Task') .'</h4>',
    'toggleButton' => [
        'class' => 'calendar-button hidden',
        'data-target' => '#calendar-modal',
    ],
    'clientOptions' => false,
]); ?>

<?php Pjax::begin([
    'id' => 'formCalendar',
    'enablePushState' => false,
    'linkSelector' => 'a.pjax-modal-campaign'
]); ?>

<?php Pjax::end(); ?>

<?php Modal::end(); ?>

<?php
Modal::begin([
    'id' => 'project-modal',
    'header' => '<h4 class="modal-title">'.Yii::t('app', 'Project') .'</h4>',
    'toggleButton' => [
        'class' => 'project-button hidden',
        'data-target' => '#project-modal',
    ],
    'clientOptions' => false,
]); ?>

<?php Pjax::begin([
    'id' => 'formProject',
    'enablePushState' => false,
    'linkSelector' => 'a.pjax-modal-project'
]); ?>

<?php Pjax::end(); ?>

<?php Modal::end(); ?>


