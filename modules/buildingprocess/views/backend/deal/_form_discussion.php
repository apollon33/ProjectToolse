<?php
use modules\field\models\ProcessFieldTemplate;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use modules\buildingprocess\models\Discussion;

/* @var $modelDocument modules\document\models\Document */
/* @var $modelDiscussion modules\buildingprocess\models\Discussion */
/* @var $processInstance modules\buildingprocess\models\ProcessInstance */
/* @var $messagesArray [] array discussion messages */
?>
<div class="header-deal-wrapper">

    <?php foreach ($processInstance->processFieldInstances as $field): ?>
        <?php if ($field->field->name === ProcessFieldTemplate::DOCUMENT_DEAL && $field->instance->process->create_folder): ?>
            <?= Html::a(Yii::t('app', 'Take me to folder'), ['/document', 'id' => $field->data],
                ['class' => 'btn btn-default', 'style' => ['margin-top' => '8px'], 'target' => '_blank' ]) ?>
            <?= Html::input('hidden', 'parentId', $field->data ,['id' => 'js-parent-folder-id']) ?>
        <?php endif; ?>
        <?php if ($field->field->name === ProcessFieldTemplate::NAME_DEAL): ?>
            <?= Html::input('hidden', 'processId', $processInstance->id ,['id' => 'js-process-id']) ?>
            <?= Html::tag('div', $field->data, ['class' => 'col-md-9 col-sm-8 col-xs-8', 'style' => ['font-size' => '18pt', 'margin-top' => '8px']]) ?>
        <?php endif; ?>
    <?php endforeach; ?>
</div>


<div class="tab-content">
    <?php $form = ActiveForm::begin([
        'action' => false,
        'options' => [
            'class' => 'form-horizontal js-discussion-form',
        ],
    ]); ?>
    <?= Html::input('hidden', 'modelDocumentId', $discussionMessagesId, ['id' => 'modelDocumentId']) ?>
    <?= $form->field($modelDiscussion, 'channel')->dropDownList(Discussion::getChannelTypes()); ?>
    <?= $form->field($modelDiscussion, 'text')->textarea(); ?>
    <?= Html::button('Save', ['class' => 'btn btn-success js-discussion-save']) ?>
    <?php ActiveForm::end(); ?>
</div>

<div class ='js-dynamic-discussion-content'>
<?= $this->render('_view_discussion_delete_message', [
    'messagesArray' => $messagesArray,
    'userId' => $userId,
]) ?>
</div>


