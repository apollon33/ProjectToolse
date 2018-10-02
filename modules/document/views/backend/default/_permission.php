<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\access\AccessManager;
use common\access\AccessInterface;
use common\models\User;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use kartik\file\FileInput;
use dosamigos\ckeditor\CKEditor;
use modules\document\models\Document;

use modules\document\models\Permission;

$fieldOptions = [
    'template' => '{label}<div class="col-md-10 col-sm-10">{input}{hint}{error}</div>',
    'labelOptions' => ['class' => 'control-label col-sm-2 col-md-2 col-xs-12'],
    'errorOptions' => ['class' => 'text-danger'],
];

$checkOptions = [
    'template' => '{label}{input}{hint}{error}',
    'labelOptions' => ['class' => 'col-md-2'],
    'errorOptions' => ['class' => 'text-danger'],
    'options' => ['class' => 'form-group field-permission-permission required col-md-2'],
];

$node = $params['node'];
$permissions = $params['listPermission'];
$permission = $params['permission'];
$modelPermission = $params['modelPermission'];
$documents = $params['documents'];

?>
<div class="row">
    <div class="col-sm-12 menu-permission">

        <?php $form = ActiveForm::begin([
            'action' => Url::to(['/document/document-permission']),
            'options' => [
                'enctype' => 'multipart/form-data',
                'class' => 'form-horizontal permission',
            ],
        ]); ?>

        <?= Html::input('hidden', 'treeNodeModify', '1') ?>
        <?= Html::input('hidden', 'parentKey', $params['parentKey']) ?>
        <?= Html::input('hidden', 'currUrl', $params['currUrl']) ?>
        <?= Html::input('hidden', 'nodeSelected', $params['nodeSelected']) ?>

        <div>
            <?= Html::input('text', 'id', $node->id, ['class' => 'hidden']); ?>

            <?= $form->field($modelPermission, 'type', $fieldOptions)->dropDownList(Permission::getTypes(),
                ['class' => 'form-control', 'id' => 'select-type']); ?>

            <?= $form->field($modelPermission, 'list', $fieldOptions)->dropDownList([],
                ['class' => 'form-control', 'id' => 'list']); ?>

            <div class="form-group hidden permission">
                <label class="control-label col-sm-3 col-md-2 col-xs-12"><?= Yii::t('app', 'Permission') ?></label>
                <div class="col-md-10 col-sm-9">
                    <?= $form->field($modelPermission, 'permission', $checkOptions)->checkbox([
                        'name' => 'permission[]',
                        'label' => Yii::t('app', 'assign'),
                        'value' => AccessManager::ASSIGN,
                        'class' => 'assign',
                        'uncheck' => false,
                    ]) ?>
                    <?= $form->field($modelPermission, 'permission', $checkOptions)->checkbox([
                        'name' => 'permission[]',
                        'label' => Yii::t('app', 'manage'),
                        'value' => AccessManager::MANAGE,
                        'uncheck' => false,
                    ]) ?>
                    <?= $form->field($modelPermission, 'permission', $checkOptions)->checkbox([
                        'name' => 'permission[]',
                        'label' => Yii::t('app', 'delete'),
                        'value' => AccessManager::DELETE,
                        'uncheck' => false,
                    ]) ?>
                    <?= $form->field($modelPermission, 'permission', $checkOptions)->checkbox([
                        'name' => 'permission[]',
                        'label' => Yii::t('app', 'create'),
                        'value' => AccessManager::CREATE,
                        'uncheck' => false,
                    ]) ?>
                    <?= $form->field($modelPermission, 'permission', $checkOptions)->checkbox([
                        'name' => 'permission[]',
                        'label' => Yii::t('app', 'update'),
                        'value' => AccessManager::UPDATE,
                        'uncheck' => false,
                    ]) ?>
                    <?= $form->field($modelPermission, 'permission', $checkOptions)->checkbox([
                        'name' => 'permission[]',
                        'label' => Yii::t('app', 'view'),
                        'value' => AccessManager::VIEW,
                        'uncheck' => false,
                    ]) ?>
                </div>
            </div>


        </div>
        <div class="pull-right">
            <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-primary']) ?>
            <?= Html::submitButton($node->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
                ['class' => 'btn btn-success permission-validation']) ?>
        </div>

        <?php ActiveForm::end(); ?>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
    <p>
        <div class="col-sm-12">
            <?php if (!empty($permissions)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th><?= Yii::t('app', 'Access type') ?></th>
                            <th><?= Yii::t('app', 'Access id') ?></th>
                            <th><?= Yii::t('app', 'Permission') ?></th>
                            <th><?= Yii::t('app', 'Document') ?></th>
                            <th><?= Yii::t('app', 'Action') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($permissions as $key => $item): ?>
                            <?php if( (integer)$item['permission'] !== 0 ) :?>
                            <tr>
                                <td><?= Permission::getTypes()[$item['access_type']] ?></td>
                                <td>
                                    <?php if (intval($item['access_type']) === AccessInterface::USER) : ?>
                                        <?= User::getList()[$item['access_id']]; ?>
                                    <?php else : ?>
                                        <?= $item['access_id'] ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= AccessManager::VIEW & $item['permission'] ? Yii::t('app', 'view') : '' ?>
                                    <?= AccessManager::UPDATE & $item['permission'] ? Yii::t('app', 'update') : '' ?>
                                    <?= AccessManager::CREATE & $item['permission'] ? Yii::t('app', 'create') : '' ?>
                                    <?= AccessManager::DELETE & $item['permission'] ? Yii::t('app', 'delete') : '' ?>
                                    <?= AccessManager::ASSIGN & $item['permission'] ? Yii::t('app', 'assign') : '' ?>
                                    <?= AccessManager::MANAGE & $item['permission'] ? Yii::t('app', 'manage') : '' ?>
                                </td>
                                <?php $document = $documents[$item['instance_id']] ?>
                                <td><?= $document->name; ?></td>
                                <td><?= (integer)$item['instance_id'] === $node['id'] ? (($document->getUOwner()->id ?? 0) === (int)$item['access_id']?
                                        '<h5>' . Yii::t('app', 'Owner') . '<h5>' :
                                        Html::a(
                                            Yii::t('app', 'Remove'),
                                            [
                                                Url::to('permission-remove'),
                                                'id' => $node->id,
                                                'access_type' => $item['access_type'],
                                                'access_id' => $item['access_id'],
                                                'permission' => false
                                            ],
                                            [
                                                'class' => 'btn btn-danger',
                                            ])) :  Html::a(
                                        Yii::t('app', 'Remove'),
                                        [
                                            Url::to('permission-remove'),
                                            'id' => $node->id,
                                            'access_type' => $item['access_type'],
                                            'access_id' => $item['access_id'],
                                            'permission' => true
                                        ],
                                        [
                                            'class' => 'btn btn-danger',
                                        ])  ;
                                    ?></td>

                            </tr>
                            <?php endif;?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
    <p class="bg-primary"><?= !empty($permission) ? Yii::t('app',
            'Permission inherited from the parent') : Yii::t('app', 'Owner of the folder'); ?></p>
    <?php endif; ?>

</div>
</div>
