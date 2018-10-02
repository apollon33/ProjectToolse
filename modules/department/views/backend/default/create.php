<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\department\models\Department */

$this->title = Yii::t('app', 'Add Department');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Departments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="position-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'referrer' => $referrer,
    ]) ?>

</div>