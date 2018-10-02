<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model modules\client\models\Counterparty */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Counterparties'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$content = Html::a(Yii::t('app', 'Add Contact'), '/client/counterparty/contacts?id=' . $model->id, ['class' => 'btn btn-primary']);
if (!empty($model->contactPerson)) {
    foreach ($model->contactPerson as $contact) {
        $content .= DetailView::widget([
            'model' => $contact,
            'attributes' => [
                'payer_address',
                'phone',
                'email',
            ],
        ]);
        $content .= Html::a(Yii::t('app', 'Update Contact'), '/client/counterparty/update-contacts?id=' . $contact->id, ['class' => 'btn btn-success']);
        $content .= Html::a(Yii::t('app', 'Delete Contact'), '/client/counterparty/delete-contacts?id=' . $contact->id, ['class' => 'btn btn-danger']);
    }

}
?>
<div class="company-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?php $activeTab = !empty($activeTab) ? $activeTab : 'general'; ?>
    <?= Tabs::widget([
        'items' => [
            [
                'label' => 'General',
                'active' => empty($activeTab) || $activeTab == 'general',
                'options' => ['id' => 'general'],
                'content' => DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        'name',
                        'type',
                        'registration_number',
                        'vat',
                        'timezone',
                        [
                            'attribute' => 'country',
                            'value' => function ($module) {
                                return $module->countryName;
                            },
                        ],
                        'country',
                        'city',
                        'address',
                        'payment_method',
                        'currency',
                        'bank_name',
                        'iban',
                        'swift',
                        'comments',
                    ],
                ])

            ],
            [
                'label' => 'Contact Person',
                'active' => empty($activeTab) || $activeTab == 'contact',
                'options' => ['id' => 'contact'],
                'content' => $content
            ],
        ],
    ]);?>

</div>
