<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\i18n\models\Language */

$this->title = Yii::t('app', 'Add Language');
?>
<div class="language-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
