<?php
use yii\helpers\Html;
use kartik\form\ActiveForm;

use common\helpers\Toolbar;

use common\access\AccessManager;

extract($params);
?>

<div class="position-update">

    <div class="position-form col-lg-12">

        <?php echo Html::hiddenInput('come_back', 'department-'.$node->id);?>

        <h1>Update Department</h1>

        <?php $form = ActiveForm::begin(['action' => '/department/'.$node->id.'/update',]); ?>

        <div class="panel panel-default">

            <div class="panel-heading"><b><?= Yii::t('app', 'Department'); ?></b></div>

            <div class="panel-body">

                <?= $form->field($node, 'name')->textInput(['maxlength' => true]); ?>

                <?= $form->field($node, 'description')->textInput(['maxlength' => true]); ?>

            </div>
        </div>

        <div class="pull-left">
            <?= Toolbar::deleteDeprtmentStructure($node->id); ?>
        </div>
        <div class="pull-right">
            <?=  Yii::$app->user->can('department.backend.default:' . AccessManager::UPDATE) ?
                Html::submitButton(Yii::t('app', 'Save Department'), ['class' => 'btn btn-warning']) : ''; ?>
        </div>
        <br />
        <br />
        <br />

        <?php ActiveForm::end(); ?>

        <div class="clearfix"></div>
    </div>
</div>