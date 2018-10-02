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
        'disabled' => ProcessFieldTemplate::statusClientField($fieldOption->field_id),
    ]); ?>
<?php endforeach; ?>

<div class="footer-deal-wrapper">
    <div class="panel-body footer-deal-container">
        <div class="col-md-12">
            <?=
            Html::tag('div',
                Html::tag('label', Yii::t('app', !empty($creater) ? 'Responsible person' : 'Creator'),
                    ['class' => 'control-label col-sm-4 col-md-4 col-xs-12']) .
                Html::tag('div', Html::tag('label', User::getList()[!empty($creater) ? $creater : Yii::$app->user->id],
                        ['class' => 'form-control']) .
                    Html::tag('div', '', ['class' => 'text-danger'])
                    , ['class' => 'col-md-6 col-sm-6 col-xs-12']),
                ['class' => 'form-group']) ?>
        </div> 
        <?php if (!$processInstance->isNewRecord) : ?>
            <?php if(!empty($creater)):?>
                <div class="pull-left footer-deal-button-left">
                    <?= Html::a(Yii::t('app', 'ADD DISCUSSION'), '#discussion', ['data-toggle' => "tab", 'aria-expanded' => "true", 'class' => 'btn footer-deal-add-discussion']) ?>
                </div>
            <?php endif;?>
            <div class="pull-right footer-deal-button-right">
                <?= Html::a(Yii::t('app', 'SIGN and PROCEED'), !empty($path) ? $path : '#',
                    ['class' => 'btn footer-deal-sing']) ?>
                <input type="hidden" class="js-process-instance-id" value="<?= $processInstance->id ?>">
            </div>


            <?php if (Yii::$app->controller->action->id != 'create') : ?>
                <div class="pull-right" style="margin-right: 20px;">
                    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn footer-deal-save approve js-footer-deal-save']) ?>
                </div>
            <?php endif; ?>
        <?php else : ?>
            <?php if(!empty($creater)):?>
                <div class="pull-left footer-deal-button-left">
                    <?php if (empty($messages)) : ?>
                        <?= Html::a(Yii::t('app', 'ADD DISCUSSION'), '#discussion', ['data-toggle' => "tab", 'aria-expanded' => "true", 'class' => 'btn footer-deal-add-discussion']) ?>
                    <?php else : ?>
                        <?= Html::a(Yii::t('app', 'VIEW DISCUSSION'), '#discussion', ['data-toggle' => "tab", 'aria-expanded' => "true", 'class' => 'btn footer-deal-add-discussion']) ?>
                    <?php endif; ?>
                </div>
            <?php endif;?>
            <div class="pull-right" style="margin-right: 20px;">
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn footer-deal-save js-footer-deal-save']) ?>
            </div>
        <?php endif; ?>
    </div>
</div>





