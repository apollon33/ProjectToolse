<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use modules\holiday\models\Holiday;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model modules\actioncalendar\models\Event */

?>
<div class="event-view">


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            [
                'attribute' => 'type',
                'value' => function ($data) {
                    return Holiday::getTypeHoliday()[$data->type];
                },
            ],
            'location',
            'description',
            [
                'attribute' => 'created_by',
                'value' => function ($data) {
                    return User::getList()[$data->created_by];
                },
            ],
            [
                'attribute' => 'Interval',
            ],

        ],
    ]) ?>
</div>
