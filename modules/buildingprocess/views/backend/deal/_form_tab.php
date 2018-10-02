<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use \modules\client\models\Client;
use modules\field\models\ProcessFieldTemplate;


/* @var $this yii\web\View */
/* @var $model modules\buildingprocess\models\ProcessTemplate */
/* @var $form yii\widgets\ActiveForm */
/* @var $formStageChildren [] array form stages children */
/* @var $validationFormStage [] array validation stages children */
/* @var $processInstance model processInstance */

?>
<?php Modal::begin([
    'id' => 'assing-modal-client',
    'header' => '<h4 class="modal-title-client">' . Yii::t('app', 'Assign') . '</h4>',
    'toggleButton' => [
        'class' => 'assing-button-client hidden',
        'data-target' => '#assing-modal-client',
    ],
    'clientOptions' => false,
]); ?>


<div class="alert">
    <?=Html::dropDownList('client_list', 'Select...', Client::getList(),['class' => 'form-control']);?>
    <div class="pull-right">
        <?= Html::a(Yii::t('app', 'Cancel'), '#', [
            'class' => 'btn btn-primary',
            'data-dismiss' => 'modal',
            'aria-hidden' => 'true'
        ]) ?>
        <?= Html::submitButton(Yii::t('app', 'Assign'),
            ['class' => 'btn btn-success client']) ?>
    </div>
</div>


<?php Modal::end(); ?>
<div class="panel-body header-deal-wrapper">
<?php foreach ($processInstance->processFieldInstances as $field): ?>
    <?php if ($field->field->name === ProcessFieldTemplate::CLIENT_FIELD_NAME) : ?>
    <div class="client">
        <?php if ($field->data == 0) : ?>
            <?= Html::button(Yii::t('app', 'Client link'),
                ['class' => 'btn btn-default client_btn', 'style' => ['margin' => '8px 5px 0px 0px'] ]) ?>
            <?= Html::input('hidden', 'clientId', $field->data ,['id' => 'js-parent-folder-id']) ?>
        <?php else : ?>
            <?php if (empty($stage->getPrev()) && $isAllowedToEditStage) : ?>
                <div class="btn_client btn">
                    <?= Yii::t('app', Client::getName($field->data)); ?>
                    <div class="poster-client">
                        <?= Html::a(Yii::t('app', 'Client view'), ['/client/' . $field->data . '/view'],
                            ['class' => 'btn btn-client', 'target' => '_blank']) ?>
                        <?= Html::a(Yii::t('app', 'Client edit'), '#',
                            ['class' => 'btn client_btn']) ?>
                    </div>
                </div>
            <?php else: ?>
                <?= Html::a(Yii::t('app', Client::getName($field->data)), ['/client/' . $field->data . '/view'],
                    ['class' => 'btn btn-client', 'style' => ['margin-top' => '8px'], 'target' => '_blank']) ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>
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
<?php Pjax::begin([
    'id' => 'formSaving',
    'timeout' => false,
    'enablePushState' => false,
    'linkSelector'    => 'a.pjax-modal-saving',
    'options' => [
        'type' => 'POST',
    ],
    'clientOptions' => [
        'cache' => false,
    ]
]);?>

<?= $this->render('_form_instance', [
    'formStageChildren' => $formStageChildren,
    'processInstance' => $stage

 ])?>

<?php Pjax::end();?>

