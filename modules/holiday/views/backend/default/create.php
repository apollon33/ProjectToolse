<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\holiday\models\Holiday */

$this->title = Yii::t('app', 'Add Item');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Holidays'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="holiday-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
