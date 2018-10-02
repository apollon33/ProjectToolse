<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\buildingprocess\models\BuildingProcess */

$this->title = 'Add Process Building';
$this->params['breadcrumbs'][] = ['label' => 'Processes Building', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="building-process-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
