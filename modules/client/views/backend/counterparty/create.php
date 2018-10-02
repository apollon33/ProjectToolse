<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\client\models\Counterparty */

$this->title = Yii::t('app', 'Add Counterparty');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Counterparties'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
