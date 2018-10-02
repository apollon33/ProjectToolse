<?php
use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use common\access\AccessManager;
use common\access\AccessInterface;
use backend\assets\AppAsset;
use common\models\RoleForm;


$this->registerCssFile(Url::to(['/css/role.css']), ['position' => View::POS_HEAD]);
$this->registerJsFile(Url::to(['/js/role.js']), [
    'depends' => AppAsset::className(),
    'position' => View::POS_END,
]);
?>
<div class="panel-body">
    <ul>
        <?php foreach ($permissions as $permission) : ?>
            <li>
                <div><label><?= Yii::t('app', $permission[0]) ?></label></div>
                <div class="border-role">
                    <?php $parentPermission = false; ?>
                    <?php foreach ($permission as $item): ?>
                    <?php $countChains = count(explode('.', $item)); ?>
                    <?php if ($countChains === 1): ?>
                    <ul>
                        <li>
                            <div class="checkbox">
                                <label>
                                    <?php $permissionInfo = RoleForm::permissionInfo($rolePermissions, $item, AccessManager::ASSIGN) ?>
                                    <input type="checkbox" name="Permission[<?= $item ?>][]"
                                           value="<?= AccessManager::ASSIGN ?>"
                                        <?php if ($permissionInfo['access']) : ?>
                                            <?php if ($permissionInfo['access_type'] === AccessInterface::GROUP): ?>
                                                disabled=true
                                            <?php endif; ?>
                                            checked="checked"
                                        <?php endif; ?>
                                    />
                                    <?= $item . '.' . Yii::t('app', 'access') ?>
                                    <?php if ($permissionInfo['access']) : ?>
                                        <?php if ($permissionInfo['access_type'] === AccessInterface::GROUP): ?>
                                            <span>(<?= $permissionInfo['listGroups'] ?> )</span>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                </label>
                            </div>
                        </li>
                    </ul>
                    <ul>
                        <li>
                            <label>
                                <?php $permissionInfo = RoleForm::permissionInfo($rolePermissions, $item, AccessManager::MANAGE) ?>
                                <input type="checkbox" value="<?= AccessManager::MANAGE ?>"
                                       name="Permission[<?= $item ?>][]"
                                    <?php if ($permissionInfo['access']) : ?>
                                        <?php if ($permissionInfo['access_type'] === AccessInterface::GROUP): ?>
                                            disabled=true
                                        <?php endif; ?>
                                        checked="checked"
                                    <?php endif; ?>
                                />
                                <?= $item . '.' . Yii::t('app', 'manage') ?>
                                <?php if ($permissionInfo['access']) : ?>
                                    <?php if ($permissionInfo['access_type'] === AccessInterface::GROUP): ?>
                                        <span>(<?= $permissionInfo['listGroups'] ?> )</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </label>
                        </li>
                    </ul>
                    <ul>
                        <li>
                            <div class="checkbox">
                                <label>
                                    <?php $permissionInfo = RoleForm::permissionInfo($rolePermissions, $item, AccessManager::CRUD) ?>
                                    <input type="checkbox" name="Permission[<?= $item ?>][]"
                                           value="<?= AccessManager::CRUD ?>"
                                        <?php if ($permissionInfo['access']) : ?>
                                            <?php if ($permissionInfo['access_type'] === AccessInterface::GROUP): ?>
                                                disabled=true
                                            <?php endif; ?>
                                            checked="checked"
                                            <?php $parentPermission = true; ?>
                                        <?php endif; ?>
                                        <?php if ($permissionInfo['checked']): ?>
                                            class="permissions"
                                        <?php endif; ?>
                                    />
                                    <?= $item ?>
                                    <?php if ($permissionInfo['access']) : ?>
                                        <?php if ($permissionInfo['access_type'] === AccessInterface::GROUP): ?>
                                            <span>(<?= $permissionInfo['listGroups'] ?> )</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </label>
                            </div>
                            <?php endif; ?>
                            <?php if ($countChains === 2): ?>
                            <ul>
                                <li>
                                    <div class="checkbox">
                                        <label>
                                            <?php $permissionInfo = RoleForm::permissionInfo($rolePermissions, $item, AccessManager::CRUD) ?>
                                            <input type="checkbox"
                                                   value="<?= AccessManager::CRUD ?>"
                                                   name="Permission[<?= $item ?>][]"
                                                <?php if ($permissionInfo['access']) : ?>
                                                    <?php if ($permissionInfo['access_type'] === AccessInterface::GROUP): ?>
                                                        disabled=true
                                                    <?php endif; ?>
                                                    checked="checked"
                                                    <?php $parentPermission = true; ?>
                                                <?php else : ?>
                                                    <?php
                                                    if ($parentPermission) : ?>
                                                        checked="checked"
                                                        disabled=true
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <?php if ($permissionInfo['checked']): ?>
                                                    class="permissions"
                                                <?php endif; ?>
                                            />
                                            <?= $item ?>
                                            <?php if ($permissionInfo['access']) : ?>
                                                <?php if ($permissionInfo['access_type'] === AccessInterface::GROUP): ?>
                                                    <span>(<?= $permissionInfo['listGroups'] ?> )</span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($countChains === 3): ?>
                                        <ul>
                                            <li>
                                                <div class="checkbox">
                                                    <label>
                                                        <?php $permissionInfoCRUD = RoleForm::permissionInfo($rolePermissions, $item, AccessManager::CRUD) ?>
                                                        <input type="checkbox"
                                                               name="Permission[<?= $item ?>][]"
                                                               value="<?= AccessManager::CRUD ?>"
                                                            <?php if ($permissionInfoCRUD['access']) : ?>
                                                                <?php if ($permissionInfoCRUD['access_type'] === AccessInterface::GROUP): ?>
                                                                    disabled=true
                                                                <?php endif; ?>
                                                                checked="checked"
                                                            <?php else : ?>
                                                                <?php if ($parentPermission) : ?>
                                                                    checked="checked"
                                                                    disabled=true
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                            <?php if ($permissionInfoCRUD['checked']): ?>
                                                                class="permissions"
                                                            <?php endif; ?>
                                                        />
                                                        <?= $item ?>
                                                        <?php if ($permissionInfoCRUD['access']) : ?>
                                                            <?php if ($permissionInfoCRUD['access_type'] === AccessInterface::GROUP): ?>
                                                                <span>( <?= $permissionInfoCRUD['listGroups'] ?>)</span>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </label>
                                                </div>
                                                <ul>
                                                    <li>
                                                        <div class="checkbox">
                                                            <?php $permissionInfo = RoleForm::permissionInfo($rolePermissions, $item, AccessManager::DELETE) ?>
                                                            <?= Html::checkbox('Permission[' . $item . '][]',
                                                                ($permissionInfo['access'] || $parentPermission) ? true : false,
                                                                [
                                                                    'label' => Yii::t('app', 'delete'),
                                                                    'class' => $permissionInfo['checked'] ? 'permissions' : '',
                                                                    'value' => AccessManager::DELETE,
                                                                    'disabled' => ($permissionInfo['access'] && $permissionInfo['listGroups'] || $permissionInfoCRUD['access']) || $parentPermission ? true : false,
                                                                ]); ?>
                                                            <?php
                                                            if ($permissionInfo['access']) : ?>
                                                                <?php if ($permissionInfo['access_type'] === AccessInterface::GROUP): ?>
                                                                    <span>(<?= $permissionInfo['listGroups'] ?> )</span>
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                        </div>

                                                    </li>
                                                    <li>
                                                        <div class="checkbox">
                                                            <?php $permissionInfo = RoleForm::permissionInfo($rolePermissions, $item, AccessManager::CREATE) ?>
                                                            <?= Html::checkbox('Permission[' . $item . '][]',
                                                                ($permissionInfo['access'] || $parentPermission) ? true : false,
                                                                [
                                                                    'label' => Yii::t('app', 'create'),
                                                                    'class' => $permissionInfo['checked'] ? 'permissions' : '',
                                                                    'value' => AccessManager::CREATE,
                                                                    'disabled' => ($permissionInfo['access'] && $permissionInfo['listGroups'] || $permissionInfoCRUD['access']) || $parentPermission ? true : false,
                                                                ]); ?>
                                                            <?php
                                                            if ($permissionInfo['access']) : ?>
                                                                <?php if ($permissionInfo['access_type'] === AccessInterface::GROUP): ?>
                                                                    <span>(<?= $permissionInfo['listGroups'] ?> )</span>
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                        </div>

                                                    </li>
                                                    <li>
                                                        <div class="checkbox">
                                                            <?php $permissionInfo = RoleForm::permissionInfo($rolePermissions, $item, AccessManager::UPDATE) ?>
                                                            <?= Html::checkbox('Permission[' . $item . '][]',
                                                                ($permissionInfo['access'] || $parentPermission) ? true : false,
                                                                [
                                                                    'label' => Yii::t('app', 'update'),
                                                                    'class' => $permissionInfo['checked'] ? 'permissions' : '',
                                                                    'value' => AccessManager::UPDATE,
                                                                    'disabled' => ($permissionInfo['access'] && $permissionInfo['listGroups'] || $permissionInfoCRUD['access']) || $parentPermission ? true : false,
                                                                ]); ?>
                                                            <?php
                                                            if ($permissionInfo['access']) : ?>
                                                                <?php if ($permissionInfo['access_type'] === AccessInterface::GROUP): ?>
                                                                    <span>(<?= $permissionInfo['listGroups'] ?> )</span>
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                        </div>

                                                    </li>
                                                    <li>
                                                        <div class="checkbox">
                                                            <?php $permissionInfo = RoleForm::permissionInfo($rolePermissions, $item, AccessManager::VIEW) ?>
                                                            <?= Html::checkbox('Permission[' . $item . '][]',
                                                                ($permissionInfo['access'] || $parentPermission) ? true : false,
                                                                [
                                                                    'label' => Yii::t('app', 'view'),
                                                                    'class' => $permissionInfo['checked'] ? 'permissions' : '',
                                                                    'value' => AccessManager::VIEW,
                                                                    'disabled' => ($permissionInfo['access'] && $permissionInfo['listGroups'] || $permissionInfoCRUD['access']) || $parentPermission ? true : false,
                                                                ]);
                                                            ?>
                                                            <?php
                                                            if ($permissionInfo['access']) : ?>
                                                                <?php if ($permissionInfo['access_type'] === AccessInterface::GROUP): ?>
                                                                    <span>(<?= $permissionInfo['listGroups'] ?> )</span>
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                        </div>

                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </li>
                            </ul>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>