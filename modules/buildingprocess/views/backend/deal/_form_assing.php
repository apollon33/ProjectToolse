<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;

use modules\document\models\Permission;
use common\models\User;

?>
<?php Modal::begin([
    'id' => 'assing-modal',
    'header' => '<h4 class="modal-title">' . Yii::t('app', 'Assign') . '</h4>',
    'toggleButton' => [
        'class' => 'assing-button hidden',
        'data-target' => '#assing-modal',
    ],
    'clientOptions' => false,
]); ?>


    <div class="alert">

        <?php $form = ActiveForm::begin([
            'action' => [Url::to(['deal-permission']), 'type' => Yii::$app->request->get('type'), 'id' => !empty($processInstance->getNext()) ? $processInstance->getNextId() : ''],
            'options' => [
                'enctype' => 'multipart/form-data',
                'class' => 'form-horizontal permission',
            ],
        ]); ?>

        <?= $form->field($modelPermission, 'type',
            ['options' => ['class' => 'hidden']])->dropDownList(Permission::getTypes(), ['value' => 1]); ?>

        <?= $form->field($modelPermission, 'list')->dropDownList(User::getList(), ['class' => 'form-control']); ?>

        <?= $form->field($processInstance, 'process_id', ['options' => ['class' => 'hidden']])->textInput(['value' => $processTemplate->id]);?>

        <?= $form->field($processInstance, 'owner', ['options' => ['class' => 'hidden']])->textInput(['value' => empty($processInstance->parent) ? $processInstance->owner : '']);?>

        <?= $form->field($processInstance, 'parent', ['options' => ['class' => 'hidden']])->textInput(['value' => empty($processInstance->parent) ? $processInstance->id : $processInstance->parent]);?>

        <div class="pull-right">
            <?= Html::a(Yii::t('app', 'Cancel'), '#', [
                'class' => 'btn btn-primary',
                'data-dismiss' => 'modal',
                'aria-hidden' => 'true'
            ]) ?>
            <?= Html::submitButton(Yii::t('app', 'Assign'),
                ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>


<?php Modal::end(); ?>