<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Tabs;
use yii\jui\Accordion;

/* @var $this yii\web\View */
/* @var $model modules\client\models\Client */

$this->title = $client->client_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$content = Html::a(Yii::t('app', 'Add Counterparty'), ['/client/counterparty/create', 'id' => $client->id], ['class' => 'btn btn-primary']);
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
                            [
                                'attribute' => 'country',
                                'value' => function ($module) {
                                    return $module->countryName;
                                },
                            ],
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
    }

}

?>
<div class="client-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $client->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Trash'), ['archive', 'id' => $client->id], [
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
                'label' => Yii::t('app', 'General'),
                'active' => empty($activeTab) || $activeTab == 'general',
                'options' => ['id' => 'general'],
                'content' => DetailView::widget([
                    'model' => $client,
                    'attributes' => [
                        'id',
                        'client_name',
                        'company_name',
                        'timezone',
                        'email:email',
                        'skype',
                        'phone',
                        'country.name',
                        'description',
                    ],
                ])

            ],
            [
                'label' => Yii::t('app', 'Counterparty information'),
                'active' => empty($activeTab) || $activeTab == 'counterparty_information',
                'option' => ['id' => 'counterparty_information'],
                'content' => $content
            ],
        ],
    ]);?>

</div>
