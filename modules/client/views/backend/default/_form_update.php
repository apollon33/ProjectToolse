<?php

use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use modules\client\models\Counterparty;
use modules\country\models\Country;
use yii\jui\Accordion;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\client\models\Client */
/* @var $form yii\widgets\ActiveForm */
$content = Html::a(Yii::t('app', 'Add Counterparty'), '/client/counterparty/create?id=' . $client->id, ['class' => 'btn btn-primary']);
if (!empty($client->counterparties)) {
    foreach ($client->counterparties as $counterparty) {
        $content .= Accordion::widget([
            'items' => [
                [
                    'header' => $counterparty->name,
                    'content' => DetailView::widget([
                        'model' => $counterparty,
                        'attributes' => [
                            'name',
                            'type',
                            'registration_number',
                            'vat',
                            'timezone',
                            'country',
                            'city',
                            'address',
                            'payment_method',
                            'currency',
                            'bank_name',
                            'comments',
                        ],
                    ]),
                    'options' => ['tag' => 'div'],
                ],

            ],
            'options' => ['tag' => 'div'],
            'itemOptions' => ['tag' => 'div'],
            'headerOptions' => ['tag' => 'h3'],
            'clientOptions' => ['collapsible' => true],
        ]);
        $content .= Html::a(Yii::t('app', 'Update Contact'), '/client/counterparty/update?id=' . $counterparty->id, ['class' => 'btn btn-success']);
        $content .= Html::a(Yii::t('app', 'Delete Contact'), '/client/counterparty/delete?id=' . $counterparty->id, ['class' => 'btn btn-danger']);
    }

}
?>
<div class="profile-form col-lg-8 alert alert-info">


    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal']]); ?>



    <?php $activeTab = !empty($activeTab) ? $activeTab : 'general'; ?>

    <?= Tabs::widget([
        'items' => [
            [
                'label' => Yii::t('app', 'General'),
                'active' => empty($activeTab) || $activeTab == 'general',
                'options' => ['id' => 'general'],
                'content' =>
                    '<div class="form-group"><div class="col-sm-12">'
                    . $form->field($client, 'client_name')->textInput(['maxlength' => true])
                    . $form->field($client, 'company_name')->textInput(['maxlength' => true])
                    . $form->field($client, 'email')->textInput(['maxlength' => true])
                    . $form->field($client, 'skype')->textInput(['maxlength' => true])
                    . $form->field($client, 'phone')->textInput(['maxlength' => true])
                    . $form->field($client, 'country_id')->dropDownList(Country::getList(),['prompt' => 'Select...'])
                    . $form->field($client, 'timezone')->textInput(['maxlength' => true])
                    . $form->field($client, 'description')->textarea(['rows' => 6])
                    .'</div></div>'
                    .'<div class="pull-right">'
                    . Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-primary'])
                    . Html::submitButton($client->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-success'])
                    .'</div>'

            ],
            [
                'label' => Yii::t('app', 'Counterparty information'),
                'active' => empty($activeTab) || $activeTab == 'counterparty_information',
                'option' => ['id' => 'counterparty_information'],
                'content' => $content,
            ],
        ],
    ]);?>

    <?php ActiveForm::end(); ?>



    <div class="clearfix"></div>

</div>