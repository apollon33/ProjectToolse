<?php
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use modules\buildingprocess\models\Discussion;
use kartik\tree\TreeView;
use kartik\file\FileInput;
use modules\document\models\Document;
use yii\helpers\Url;

$breadcrumbs = $params['breadcrumbs'];
$parentKey = $params['parentKey'];
$node = $params['node'];
$messages = array_key_exists('messages', $params) ? $params['messages'] : false;
$userId =  array_key_exists('userId', $params) ? $params['userId'] : false ;
$modelDiscussion =  array_key_exists('modelDiscussion', $params) ? $params['modelDiscussion'] : false;
$nodeType = isset($node->document_type) ? $node->document_type: $params['nodeType'];

$depth = ArrayHelper::getValue($breadcrumbs, 'depth');
$glue = ArrayHelper::getValue($breadcrumbs, 'glue');
$activeCss = ArrayHelper::getValue($breadcrumbs, 'activeCss');
$untitled = ArrayHelper::getValue($breadcrumbs, 'untitled');
$name = $node->getBreadcrumbs($depth, $glue, $activeCss, $untitled);
if ($node->isNewRecord && !empty($parentKey) && $parentKey !== TreeView::ROOT_KEY) {
    /**
     * @var Tree $modelClass
     * @var Tree $parent
     */
    $depth = empty($breadcrumbsDepth) ? null : intval($breadcrumbsDepth) - 1;
    if ($depth === null || $depth > 0) {
        $parent = $modelClass::findOne($parentKey);
        $name = $parent->getBreadcrumbs($depth, $glue, null) . $glue . $name;
    }
}
?>

<h1><?= $node->name ?></h1>

<div class="copy-link-document">
    <a class="btn btn-success copy-url" href="<?= Url::to(['/document', 'id' => $node->id]); ?>">
        <span class="glyphicon glyphicon-copy" aria-hidden="true"></span>
        <?= Yii::t('app', 'Link to document')?>
    </a>
</div>

<?php if (!$node->isNewRecord) : ?>

    <?php if (!empty($node->user)) : ?>
        <div class="pull-left">
            <b><?= Yii::t('app', 'Owner') ?>:</b>
            <?= $node->user->username ?>
        </div>
    <?php endif; ?>

    <div class="pull-right">
        <small>
            <b><?= Yii::t('app', 'Created') ?>:</b>
            <?= $node->created ?>
            &nbsp;
            <b><?= Yii::t('app', 'Updated') ?>:</b>
            <?= $node->updated ?>
        </small>
    </div>

    <div class="clearfix"></div>

    <br />

    <div>
        <?php
        $stringLength = 2000;
        $readMore = Html::tag('span', '... ' . Html::a(Yii::t('app', 'Read more') . ' ' . Html::tag('span', null, ['class' => 'glyphicon glyphicon-arrow-right']), ['#'], ['class' => 'btn btn-sm btn-success']), ['id' => 'read-more']);
        ?>
        <?php if (!$messages && $node->description != '[]'): ?>
            <div class="text-view-document">
                <?= $node->description?>
            </div>
            <?php if (strlen($node->description) > $stringLength): ?>
                <?= $readMore ?>
            <?php endif; ?>
        <?php endif;?>
        <?php if (is_array($messages)): ?>
            <div class="tab-content">
                <?php $form = ActiveForm::begin([
                    'action' => '',
                    'options' => [
                        'class' => 'form-horizontal js-discussion-form',
                    ],
                ]); ?>
                <?= Html::input('hidden', 'modelDocumentId', $node->id, ['id' => 'modelDocumentId']) ?>
                <?= $form->field($modelDiscussion, 'channel')->dropDownList(Discussion::getChannelTypes()); ?>
                <?= $form->field($modelDiscussion, 'text')->textarea(); ?>
                <?= Html::button('Save', ['class' => 'btn btn-success js-discussion-document-tree-save']) ?>
                <?php ActiveForm::end(); ?>
            </div>
            <div class ='js-dynamic-discussion-content'>
                <?= $this->render('_show_discussion_messages', [
                    'messages' => $messages,
                    'userId' => $userId,
                ]) ?>
            </div>
        <?php endif;?>
      
        <?php if ($nodeType === Document::NODE_TYPE_ATTACHMENT ): ?>
            <?php
            $lastFileName = $node->attachments[0]->lastVersion->filename ;
            ?>
        <div class = 'fix_attachment_size' >

            <?= FileInput::widget([
                'name' => 'attachment',
                'pluginOptions' => [
                    'deleteUrl' => false,
                    'initialPreviewShowDelete' => false,
                    'initialPreview' => [
                        $node->attachments[0]->getDownloadUrl($node->id, $lastFileName ),
                    ],
                    'initialPreviewAsData' => true,
                    'initialPreviewConfig' => [
                        ['caption' => $node->attachments[0]->name, 'downloadUrl' => $node->attachments[0]->getDownloadUrl($node->id, $lastFileName )],
                    ],
                    'overwriteInitial' => false,
                    'maxFileSize' => 2800,
                    'showUpload' => false,
                    'showRemove' =>  false,
                    'showBrowse' => false,
                    'showCaption' => false,
                    'showClose' => false,
                ]
            ]); ?>
        </div>
        <?php endif; ?>
    </div>
    <?php if (is_array($node->attachments)): ?>
        <?php foreach ($node->attachments as $attachment): ?>
            <div class="dropdown btn-group">
                <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" >
                    <?= $attachment->name ?>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <?php foreach ($attachment->attachmentEntities as $version): ?>
                        <li><a class="dropdown-item" href="<?= $attachment->getDownloadUrl($node->id, $version->filename ) ?> "><?= $attachment->name . ' ' . 'version ' . $version->version ?></a></li>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>

