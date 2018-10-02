<?php
use yii\helpers\Html;
use common\access\AccessManager;

?>

<ul>
    <?php
    foreach ($permissions as $permission) : ?>
        <li>
            <div><label><?= Yii::t('app', $permission[0]) ?></label></div>
            <div class="border-role">
                <?php $parentPermission = false;
                foreach ($permission as $item): ?>
                <?php $countChains = count(explode('.', $item)); ?>
                <?php if ($countChains === 1): ?>
                <ul>
                    <li>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="Permission[<?= $item ?>][]"
                                       value="<?= AccessManager::ASSIGN ?>"
                                    <?php
                                    if (array_key_exists($item, $rolePermissions)) :
                                        if ((integer)$rolePermissions[$item]['permission'] & AccessManager::ASSIGN) : ?>
                                            checked="checked"
                                        <?php endif; ?>
                                    <?php endif; ?>
                                />
                                <?= $item . '.' . Yii::t('app', 'access') ?>
                            </label>
                        </div>
                    </li>
                </ul>
                <ul>
                    <li>
                        <label>
                            <input type="checkbox" value="<?= AccessManager::MANAGE ?>"
                                   name="Permission[<?= $item ?>][]"
                                <?php
                                if (array_key_exists($item, $rolePermissions)) :
                                    if ((integer)$rolePermissions[$item]['permission'] & AccessManager::MANAGE) : ?>
                                        checked="checked"
                                    <?php endif; ?>
                                <?php endif; ?>
                            />
                            <?= $item . '.' . Yii::t('app', 'manage') ?>
                        </label>
                    </li>
                </ul>
                <ul>
                    <li>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="permissions" name="Permission[<?= $item ?>][]"
                                       value="<?= AccessManager::CRUD ?>"
                                    <?php
                                    if (array_key_exists($item, $rolePermissions)) :
                                        if ((integer)$rolePermissions[$item]['permission'] & AccessManager::CRUD) : ?>
                                            checked="checked"
                                            <?php $parentPermission = true; endif; ?>
                                    <?php endif; ?>
                                />
                                <?= $item ?>
                            </label>
                        </div>
                        <?php endif; ?>
                        <?php if ($countChains === 2): ?>
                        <ul>
                            <li>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox"
                                               class="permissions"
                                               value="<?= AccessManager::CRUD ?>"
                                               name="Permission[<?= $item ?>][]"
                                            <?php
                                            if (array_key_exists($item, $rolePermissions)) :
                                                if ((integer)$rolePermissions[$item]['permission'] & AccessManager::CRUD) :?>
                                                    checked="checked"
                                                    <?php $parentPermission = true; endif; ?>
                                            <?php else : ?>
                                                <?php
                                                if ($parentPermission) : ?>
                                                    checked="checked"
                                                    disabled=true
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        />
                                        <?= $item ?>
                                    </label>
                                </div>
                                <?php endif; ?>
                                <?php if ($countChains === 3): ?>
                                    <ul>
                                        <li>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="permissions"
                                                           name="Permission[<?= $item ?>][]"
                                                           value="<?= AccessManager::CRUD ?>"
                                                        <?php
                                                        if (array_key_exists($item, $rolePermissions)) :
                                                            if (AccessManager::CRUD === ((integer)$rolePermissions[$item]['permission'] & AccessManager::CRUD)) { ?>
                                                                checked="checked"
                                                            <?php } ?>
                                                        <?php else: ?>
                                                            <?php
                                                            if ($parentPermission) : ?>
                                                                checked="checked"
                                                                disabled=true
                                                            <?php endif; ?>
                                                        <?php endif; ?>

                                                    />
                                                    <?= $item ?>
                                                </label>
                                            </div>
                                            <ul>
                                                <li>
                                                    <div class="checkbox">
                                                        <?= Html::checkbox('Permission[' . $item . '][]',
                                                            (array_key_exists($item,
                                                                    $rolePermissions) && (integer)$rolePermissions[$item]['permission'] & AccessManager::DELETE) || $parentPermission ? true : false,
                                                            [
                                                                'label' => Yii::t('app', 'delete'),
                                                                'class' => 'permissions',
                                                                'value' => AccessManager::DELETE,
                                                                'disabled' => (array_key_exists($item,
                                                                        $rolePermissions) && (integer)$rolePermissions[$item]['permission'] === AccessManager::CRUD) || $parentPermission ? true : false,
                                                            ]); ?>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="checkbox">
                                                        <?= Html::checkbox('Permission[' . $item . '][]',
                                                            (array_key_exists($item,
                                                                    $rolePermissions) && (integer)$rolePermissions[$item]['permission'] & AccessManager::CREATE) || $parentPermission ? true : false,
                                                            [
                                                                'label' => Yii::t('app', 'create'),
                                                                'class' => 'permissions',
                                                                'value' => AccessManager::CREATE,
                                                                'disabled' => (array_key_exists($item,
                                                                        $rolePermissions) && (integer)$rolePermissions[$item]['permission'] === AccessManager::CRUD) || $parentPermission ? true : false,
                                                            ]); ?>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="checkbox">
                                                        <?= Html::checkbox('Permission[' . $item . '][]',
                                                            (array_key_exists($item,
                                                                    $rolePermissions) && (integer)$rolePermissions[$item]['permission'] & AccessManager::UPDATE) || $parentPermission ? true : false,
                                                            [
                                                                'label' => Yii::t('app', 'update'),
                                                                'class' => 'permissions',
                                                                'value' => AccessManager::UPDATE,
                                                                'disabled' => (array_key_exists($item,
                                                                        $rolePermissions) && (integer)$rolePermissions[$item]['permission'] === AccessManager::CRUD) || $parentPermission ? true : false,
                                                            ]); ?>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="checkbox">
                                                        <?= Html::checkbox('Permission[' . $item . '][]',
                                                            (array_key_exists($item,
                                                                    $rolePermissions) && (integer)$rolePermissions[$item]['permission'] & AccessManager::VIEW) || $parentPermission ? true : false,
                                                            [
                                                                'label' => Yii::t('app', 'view'),
                                                                'class' => 'permissions',
                                                                'value' => AccessManager::VIEW,
                                                                'disabled' => (array_key_exists($item,
                                                                        $rolePermissions) && (integer)$rolePermissions[$item]['permission'] === AccessManager::CRUD) || $parentPermission ? true : false,
                                                            ]);
                                                        ?>
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