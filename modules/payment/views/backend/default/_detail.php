<?php
use yii\widgets\DetailView;
?>



<div class="tab-content">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'user.fullName',
            [   'attribute'=>'amount',
                'value' => $model->amount." ₴",
            ],
            [   'attribute'=>'tax_profit',
                'value' => $model->tax_profit." ₴",
            ],
            [   'attribute'=>'tax_war',
                'value' => $model->tax_war." ₴",
            ],
            [   'attribute'=>'tax_pension',
                'value' => $model->tax_pension." ₴",
            ],
            [   'attribute'=>'payout',
                'value' => $model->payout." ₴",
            ],
        ],
    ]) ?>
</div>
<div class="clear"></div>