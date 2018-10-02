<?php
use yii\helpers\Html;
use kartik\form\ActiveForm;
use modules\department\models\Department;
use common\helpers\Toolbar;

use common\access\AccessManager;
extract($params);
?>

<div class="position-update">

    <div class="position-form col-lg-12">

        <?php echo Html::hiddenInput('come_back', 'position-'.$node->id);?>

        <h1>Update Position</h1>

        <?php $form = ActiveForm::begin(['action' => '/position/'.$node->id.'/update',]); ?>

        <div class="panel panel-default">

            <div class="panel-heading"><b><?= Yii::t('app', 'Position') ?></b></div>

            <div class="panel-body">

                <?= $form->field($node, 'name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($node, 'department_id')->dropdownList(Department::getList()) ?>

            </div>
        </div>

        <div class="pull-left">
            <?= Toolbar::deletePositionStructure($node->id); ?>
        </div>
        <div class="pull-right">
            <?= Yii::$app->user->can('position.backend.default:' . AccessManager::UPDATE) ? Html::submitButton(Yii::t('app', 'Save Position'),
                ['class' => 'btn btn-warning']) : ''; ?>
        </div>
        <br />
        <br />
        <br />

        <?php ActiveForm::end(); ?>

        <div class="clearfix"></div>
    </div>
</div>