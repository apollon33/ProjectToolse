<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use modules\project\models\Project;
use kartik\select2\Select2;
use modules\client\models\Client;

/* @var $this yii\web\View */
/* @var $model modules\project\models\Project */
/* @var $form yii\widgets\ActiveForm */

$session = Yii::$app->session;
$filterDate = $session->get('filterDate');
$userAgeLimit  = 100;
$datePickerRange = (date('Y') - $userAgeLimit) . ':' . (date('Y')+5);
?>


<?php $form = ActiveForm::begin([
    //'action' => (isset($path) ? Url::to([$path]): ''),
    'options' => [
        'enctype' => 'multipart/form-data',
        'class' => 'form-horizontal',
        'id' => 'projectAssignment',
        'data' => ['pjax' => true] ]]); ?>


<div class="panel panel-default">

    <div class="panel-heading"><b><?= Yii::t('app', 'Assignment') ?></b></div>

    <div class="panel-body">



        <?=  $form->field($model, 'name')->widget(Select2::classname(), [
            'data' => Project::getList(),
            'options' => ['placeholder' => 'Select a user ...', 'multiple' => true],
            'pluginOptions' => [
                'tags' => true,
                'tokenSeparators' => [',', ' '],
                'maximumInputLength' => 10,
            ],
        ])->label(Yii::t('app', 'Project')); ?>



    </div>

</div>

<div class="pull-right">
    <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-primary cancel-modal']) ?>
    <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>

<div class="clearfix"></div>

</div>