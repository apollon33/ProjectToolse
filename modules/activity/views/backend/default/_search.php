<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\User;
use modules\project\models\Project;
use kartik\datecontrol\DateControl;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model modules\activity\models\ActivitySearch */
/* @var $form yii\widgets\ActiveForm */

$fieldOptions = [
    'options' => ['class' => 'col-lg-3 pull-left'],
    'template' => '{label}<div class="col-md-9 col-sm-9">{input}{hint}{error}</div>',
    'labelOptions' => ['class' => 'control-label col-sm-3 col-md-3'],
];
?>

<div class="activity-search">

    <div class="media-form alert alert-search">

        <?php $form = ActiveForm::begin([
            'action' => ['report'],
            'method' => 'get',
            'enableClientValidation' => false,
        ]); ?>

        <span class="<?= !Yii::$app->user->identity->isAdmin() ? 'hidden' : '' ?>">
            <?= $form->field($model, 'user_id', $fieldOptions)->dropdownList(User::getList()) ?>
        </span>

        <?= $form->field($model, 'project_id', $fieldOptions)->dropdownList(Project::getList(), ['prompt' => '']) ?>

        <?= $form->field($model, 'date', $fieldOptions)->widget(DatePicker::classname(), [
            'options' => ['placeholder' => 'Select date ...'],
            'removeButton' => false,
            'pluginOptions' => [
                'format' => 'M d, yyyy',
                'autoclose'=>true
            ]
        ]) ?>

        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-default']) ?>

        <?php ActiveForm::end(); ?>

        <div class="clear"></div>

    </div>

</div>