<?php
use yii\helpers\Html;
use kartik\file\FileInput;
use common\widgets\Gallery;
use yii\helpers\Url;

/** @var \modules\activity\models\Activity $model */

?>

<?= Gallery::widget() ?>

<?= $form->field($model, 'screenshot')->widget(FileInput::classname(), [
    'pluginOptions' => [
        'initialPreview' => [
            !empty($model->screenshot)
                ? '<a href="' . Url::to(['/activity/default/image-view', 'id' => $model->id]) . '" title="' . $model->target_window .'" data-gallery>'
                . Html::img(Url::to(['/activity/default/image-view', 'id' => $model->id, 'thumbnail' => true]), ['class' => 'img-preview', 'title' => $model->target_window, 'alt' => $model->target_window])
                . '<br />'
                . '</a>'
                : null
        ],
        'overwriteInitial' => true,
        'pluginLoading' => true,
        'showCaption' => false,
        'showUpload' => false,
        'showClose' => false,
        'removeClass' => 'btn btn-danger',
        'browseClass' => 'btn btn-success',
        'browseLabel' => '',
        'removeLabel' => '',
        'browseIcon' => '<i class="glyphicon glyphicon-picture"></i>',
        'previewTemplates' => [
            'generic' => '<div class="file-preview-frame" id="{previewId}" data-fileindex="{fileindex}">
                    {content}
                </div>',
            'image' => '<div class="file-preview-frame" id="{previewId}" data-fileindex="{fileindex}">
                    <img src="{data}" class="img-preview" title="{caption}" alt="{caption}">
                </div>',
        ]
    ],
]) ?>
