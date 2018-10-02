<?php
use yii\helpers\Html;
use yii\widgets\ListView;
use common\widgets\Gallery;

$this->title = 'Statistics';
?>

<div class="activity-statistics">

    <h1 class="pull-left"><?= Html::encode($this->title) ?></h1>

    <div class="pull-right">
        <br />
        <?= Html::a(Html::tag('span', null, ['class' => 'glyphicon glyphicon-arrow-left']) . ' '. Yii::t('yii', 'Back'), ['statistics'], ['class' => 'btn btn-primary']); ?>
    </div>

    <div class="clear"></div>

    <?= Gallery::widget() ?>

    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <div>
        <?php if (count($models) > 0): ?>
            <?php foreach($models as $model): ?>
                <?= $this->render('_item', ['model' => $model]); ?>
            <?php endforeach; ?>
        <?php else: ?>
            <?= Yii::t('yii', 'No results found.') ?>
        <?php endif; ?>
    </div>

</div>
