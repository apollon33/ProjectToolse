<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\vacation\models\Vacation */

$this->title = Yii::t('app', 'Add Vacation');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Vacations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vacation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
