<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\client\models\Client */

$this->title = Yii::t('app', 'Edit Client') . ': ' . $client->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $client->id, 'url' => ['view', 'id' => $client->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="client-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form_update', [
        'client' => $client,
    ]) ?>

</div>
