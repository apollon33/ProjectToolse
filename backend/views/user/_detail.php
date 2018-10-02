<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\select2\Select2;
use common\widgets\SocialChoice;
use yii\widgets\ActiveForm;
use common\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

use common\access\AccessManager;

/* @var common\models\User $model */

?>

<?php if (!empty($model->avatar)) : ?>
    <div class="pull-left">
        <a href="<?= $model->imageUrl ?>" title="<?= $model->username ?>" data-gallery>
            <?= Html::img($model->imageThumbnailUrl, ['class' => 'img-thumbnail']) ?><br/>
        </a>
    </div>
<?php endif; ?>

<div class="pull-left">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'created_at:datetime',
            'updated_at:datetime',
            'last_login_at:datetime',
            [
                'label' => Yii::t('app', 'Date dismiss'),
                'visible' => !$model->active,
                'value' => function ($model) {
                    return date('M d, Y H:i', $model->date_dismissal);
                },
            ],
        ],
    ]) ?>
</div>

<?php if ($model->auths) : ?>
    <div class="pull-left">
        <?= SocialChoice::widget([
            'baseAuthUrl' => ['site/auth'],
            'popupMode' => false,
            'isMinimized' => true,
            'auths' => $model->auths,
            'addButtonTitle' => Yii::t('app', 'Link a new Social Account'),
        ]) ?>
    </div>
<?php endif; ?>
<?php if (Yii::$app->user->can('core.backend.admin:' . AccessManager::UPDATE)): ?>
    <div class="pull-left">
        <?php $form = ActiveForm::begin([
            'id' => 'change-role-user-form',
            'action' => Url::toRoute(['update-role']),
        ]); ?>
        <?= Html::input('hidden', 'id', $model->id); ?>
        <label for="id-role-name"><?= Yii::t('app', 'Quickly edit group'); ?></label>
        <div class="roleListName">
            <div class="input-group">

                <?=
                Select2::widget([
                    'value' => ArrayHelper::map($model->roleListName, 'name', 'name'),
                    'name' => 'roleListName',
                    'data' => ArrayHelper::map(User::getListRoles(), 'name', 'name'),
                    'options' => [
                        'placeholder' => 'Select a group ...',
                        'multiple' => true,
                        '',
                        'class' => 'form-control',
                    ],
                    'pluginOptions' => [
                        'tokenSeparators' => [',', ' '],
                        'maximumInputLength' => 10,
                    ],
                    'pluginEvents' => [
                        "change" => "function (params) {
                        var roleListName = $(this).parents('.roleListName');
                        var textDander = roleListName.find('.text-danger');
                        var cssValidation = roleListName.find('span.select2-selection.select2-selection--multiple');
                        var button = roleListName.find('button');
                        if (params.target.value === '') {
                            cssValidation.css('box-shadow', 'inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #ce8483');
                            cssValidation.css('border-color', '#843534');
                            textDander.html('" . Yii::t('app', 'Group cannot be blank') . "');
                            return button.prop('disabled', true);
                        }
                        cssValidation.css('box-shadow', '');
                        cssValidation.css('border-color', '');
                        textDander.html('');
                        return button.prop('disabled', false);
                  }",
                    ],
                ])
                ?>
                <span class="input-group-btn">
            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
          </span>
            </div>
            <div class="text-danger"></div>
            <?= Html::input('text', 'page', Yii::$app->request->get('page'), ['class' => 'hidden']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
<?php endif; ?>

<div class="clear"></div>