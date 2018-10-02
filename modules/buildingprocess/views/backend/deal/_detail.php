<?php
use yii\widgets\DetailView;

?>

<div class="pull-left">
    <?php foreach ($models as $model) : ?>
        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                'name',
                [
                    'attribute' => 'type_field',
                    'value' => function ($model) {
                        return $model->getTypeField()[$model->type_field];
                    }
                ],

            ],
        ]) ?>
    <?php endforeach; ?>
</div>

<div class="clear"></div>
