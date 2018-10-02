<?php

use yii\bootstrap\Tabs;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use modules\field\models\ProcessFieldTemplate;

use modules\buildingprocess\models\FieldWidget;

use common\models\User;

//FIELD_WIDGET::widget(\Field $model)

/* @var $this yii\web\View */

/**
 * $model Field
 */

?>
<?php if (!empty($assigned)) : ?>
    <?php $username = User::getList()[$assigned];?>
    <?=Html::tag('h5', Yii::t('app', 'You were assigned by <span>{username}</span>', [
        'username' => $username,
    ]), ['class' => 'assign']);?>
<?php endif; ?>
<?php foreach ($processField as $fieldOption): ?>
    <?= FieldWidget::widget([
        'type' => $fieldOption->type,
        'required' => $fieldOption->required,
        'option' => $fieldOption->option(),
        'disabled' => ProcessFieldTemplate::statusClientField($fieldOption->field_id) ? true : ((boolean)$fieldOption->modify && $modify ? false : true),
    ]); ?>

<?php endforeach; ?>
<?php if(!empty($messageProcessInstance)) :?>
    <div style="text-align: center">
        <div class="alert alert-warning" role="alert"><?=$messageProcessInstance?></div>
    </div>
<?php endif;?>
<div class="footer-deal-wrapper">
    <div class="panel-body footer-deal-container">
        <div class="col-md-12">
            <div class="form-group">
                <?php if(!empty($creater)):?>
                <label class="control-label col-sm-4 col-md-4 col-xs-12">
                    <?= Yii::t('app', !empty($creater) ? 'Responsible person' : 'Creator'); ?>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <label class="form-control">
                        <?= User::getList()[!empty($creater) ? $creater : Yii::$app->user->id]; ?>
                    </label>
                    <div class="text-danger"></div>
                </div>
                <?php endif;?>
            </div>
            <?php if(!empty($creater)):?>
                <div class="pull-left footer-deal-button-left">
                    <?php if (empty($messages)) : ?>
                        <?= Html::a(Yii::t('app', 'ADD DISCUSSION'), '#discussion', ['data-toggle' => "tab", 'aria-expanded' => "true", 'class' => 'btn footer-deal-add-discussion']) ?>
                    <?php else : ?>
                        <?= Html::a(Yii::t('app', 'VIEW DISCUSSION'), '#discussion', ['data-toggle' => "tab", 'aria-expanded' => "true", 'class' => 'btn footer-deal-add-discussion']) ?>
                    <?php endif; ?>
                </div>
            <?php endif;?>
            <?php if ($modify):?>
                <div class="pull-right" style="margin-right: 20px;">
                    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn footer-deal-save js-footer-deal-save']) ?>
                </div>
            <?php endif;?>
        </div>
    </div>
</div>






