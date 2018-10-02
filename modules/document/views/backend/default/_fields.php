<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use dosamigos\ckeditor\CKEditor;
use yii\bootstrap\Modal;
use modules\document\models\Document;


$fieldOptions = [
    'template' => '{label}<div class="col-md-10 col-sm-10">{input}{hint}{error}</div>',
    'labelOptions' => ['class' => 'control-label col-sm-2 col-md-2 col-xs-12'],
    'errorOptions' => ['class' => 'text-danger'],
];
$node = $params['node'];
$update = $params['update'];
$root = $params['root'];
$nodeType = (int) ($node->document_type ?? $params['nodeType']);
$attachmentDocument = isset($params['attachmentDocument']) ? $params['attachmentDocument'] : false ;
?>
<div class="row">
    <div class="col-sm-12">



        <?php $form = ActiveForm::begin([
            'action' => $root ? Url::to(['/document/document-create-root']) : ($node->isNewRecord ? Url::to(['/document/document-create']) : Url::to(['/document/document-update?id='.$node->id])),
            'options' => [
                'enctype' => 'multipart/form-data',
                'class' => 'form-horizontal',
            ]
        ]); ?>

        <?= Html::input('hidden', 'treeNodeModify', '1') ?>
        <?= Html::input('hidden', 'parentKey', $params['parentKey']) ?>
        <?= Html::input('hidden', 'currUrl', $params['currUrl']) ?>
        <?= Html::input('hidden', 'modelClass', $params['modelClass']) ?>
        <?= Html::input('hidden', 'nodeSelected', $params['nodeSelected']) ?>
        <?= Html::input('hidden', 'nodesType', $nodeType ,['class' => 'js-node-type']) ?>

        <?= $form->field($node, 'id', ['options' => ['class' => 'hidden']])->textInput([]) ?>
        <?= $form->field($node, 'name', $fieldOptions)->textInput(['disabled'=> (!$node->isNewRecord && $nodeType === Document::NODE_TYPE_ATTACHMENT) ? true : false]) ?>
        <?php if ($nodeType === Document::NODE_TYPE_ATTACHMENT): ?>
            <?= Html::input('hidden', 'lastVersion', $node->attachments[0]->lastVersion->version ?? '') ?>
        <?php endif; ?>
        <?php if ( $nodeType === Document::NODE_TYPE_DOCUMENT): ?>
            <?= $form->field($node, 'document_type', $fieldOptions)->hiddenInput(['value' => Document::NODE_TYPE_DOCUMENT ])->label(false); ?>
        <?=  $update ? $form->field($node, 'description', $fieldOptions)->widget(CKEditor::className(), [
            'preset' => 'standard',
            'options' => ['rows' => 6],
            'clientOptions' => [
                'filebrowserUploadUrl' => Url::to(['/page/upload-image/', 'id' => $node->id]),
                'toolbar' => [
                    ['Source', '-', 'Save', 'NewPage', 'Preview', '-', 'Templates'],
                    ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Print', 'SpellChecker', 'Scayt'],
                    ['Undo', 'Redo', '-', 'Find', 'Replace', '-', 'SelectAll', 'RemoveFormat'],
                    ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
                    '/',
                    ['Bold', 'Italic', 'Underline', 'Strike', '-', 'Subscript', 'Superscript'],
                    ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', 'Blockquote'],
                    ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
                    ['Link', 'Unlink', 'Anchor'],
                    ['Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak'],
                    '/',
                    ['Styles', 'Format', 'Font', 'FontSize'],
                    ['TextColor', 'BGColor'],
                    ['Maximize', 'ShowBlocks', '-', 'About'],
                    ['abbr', 'inserthtml']
                ]
            ],
        ]) : '' ?>
        <?php endif; ?>
        <?php if ($nodeType === Document::NODE_TYPE_ATTACHMENT): ?>
            <div class='fix_attachment_size'>
                <?= $form->field($node, 'document_type', $fieldOptions)->hiddenInput(['value' =>$nodeType ])->label(false) ?>
                <?php foreach ($node->attachments  as $image): ?>
                    <?php
                    $attachmentLastVersions = $node->attachments[0]->lastVersion;
                    $parent_id = $attachmentLastVersions->attachment_id;
                    ?>
                    <?= $form->field($node, "file[]")->fileInput(['name' => "attachmentFiles[$parent_id]"])->label(wordwrap($attachmentLastVersions->filename, 10, "\n", true)); ?>
                <?php endforeach; ?>
                <?php if ($nodeType === Document::NODE_TYPE_ATTACHMENT && $node->isNewRecord ):?>
                    <?= $form->field($node, "file[]")->fileInput(['name' => "attachmentFiles[]"]) ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if ( $nodeType === Document::NODE_TYPE_DOCUMENT): ?>
            <div class='fix_attachment_size'>
                <?php foreach ($node->attachments as $attachment): ?>
                    <?php
                    $attachmentLastVersions = $attachment->lastVersion;
                    $parent_id = $attachmentLastVersions->attachment_id;
                    ?>
                    <?= $form->field($node, "file[]")->fileInput(['name' => "attachmentFiles[$parent_id]"])->label(wordwrap($attachmentLastVersions->filename, 10, "\n", true)) ?>
                <?php endforeach; ?>
                <?= $form->field($node, 'file[]')->fileInput(['multiple' => true, 'name' => "attachmentFiles[]"]) ?>
            </div>
        <?php endif; ?>

        <?php if (!$node->isNewRecord) : ?>

            <?= $form->field($node, 'creator', $fieldOptions)->textInput(['disabled' => true]) ?>

            <?= $form->field($node, 'created', $fieldOptions)->textInput(['disabled' => true]) ?>

            <?= $form->field($node, 'updated', $fieldOptions)->textInput(['disabled' => true]) ?>

        <?php endif; ?>

        <div class="pull-right">
            <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-primary']) ?>
            <?= Html::submitButton($node->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>


    </div>
</div>

