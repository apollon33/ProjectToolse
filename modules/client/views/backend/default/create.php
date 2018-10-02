<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\client\models\Client */

$this->title = Yii::t('app', 'Add Client');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'client' => $client,
        'counterparties' => $counterparties,
    ]) ?>

</div>
