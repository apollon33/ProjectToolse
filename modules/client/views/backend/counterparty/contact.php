<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\client\models\Counterparty */

$this->title = Yii::t('app', 'Add Contact');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contact'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="company-form col-lg-8 alert alert-info">

        <?php $form = ActiveForm::begin(); ?>

        <div class="panel panel-default">

            <div class="panel-heading"><b><?= Yii::t('app', 'Contact') ?></b></div>

            <div class="panel-body">
                <?= $form->field($contact, 'payer_address')->textarea() ?>
                <?= $form->field($contact, 'phone')->textInput() ?>
                <?= $form->field($contact, 'email')->textInput() ?>
            </div>

        </div>

        <div class="pull-right">
            <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-primary']) ?>
            <?= Html::submitButton($contact->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <div class="clearfix"></div>

    </div>

</div>