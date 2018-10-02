<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

use modules\vacation\models\Vacation;

/**
 * $modelVacation array Vacation
 */
?>
<div class="panel-body">
    <div class="form-group size-vacation">
        <label class="control-label col-sm-4 col-md-3 col-xs-9"><?= Yii::t('app', 'Vacation days left') ?> :</label>
        <div class="col-md-2 col-sm-2">
            <span class="label label-<?= empty($leftVacation) ? 'danger' : 'success' ?>"><?= $leftVacation ?> </span>
        </div>
    </div>
    <div class="form-group size-vacation">
        <label class="control-label col-sm-4 col-md-3 col-xs-9"><?= Yii::t('app', 'Selected') ?> :</label>
        <div class="col-md-2 col-sm-2">
            <span class="label label-<?= empty($leftVacation) ? 'danger' : 'success' ?>"> <?= ($fullVacation - $leftVacation); ?></span>
        </div>
    </div>
    <?= Html::a(Yii::t('app', 'Request leave'),  ['/vacation/create-cart-user', 'id' => $userId], ['class' => 'btn btn-primary pjax-modal-vacation visible-model-vacation']) ?>
</div>
<div class="vacation-index">

    <?php foreach ($listVacation as $vacation) : ?>
        <?= DetailView::widget([
            'model' => $vacation,
            'attributes' => [
                'CurrencyList',
                'StartAt',
                [
                    'attribute' => 'approve',
                    'format' => 'raw',
                    'value' => $vacation->isApproved() ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>',
                    'widgetOptions' => [
                        'pluginOptions' => [
                            'onText' => 'Yes',
                            'offText' => 'No',
                        ],
                    ],
                ],
                [
                    'attribute' => 'Update',
                    'format' => 'raw',
                    'value' => $vacation->isNotApproved() ? Html::a(Yii::t('app', 'Update'), ['/vacation/update-user-vacation', 'id' => $vacation->id], [
                        'class' => 'btn btn-success pjax-modal-vacation visible-model-vacation',
                    ]) : '',
                ]
                
            ],
        ]); ?>
    <?php endforeach; ?>
    <?= Html::button(Yii::t('app', 'Show all vacations'), ['class' => 'btn btn-primary  js-show-all-vacations']) ?>
    <div class="hidden js-all-vacations">
        <?php foreach ($allVacationList as $vacation) : ?>
            <?= DetailView::widget([
                'model' => $vacation,
                'attributes' => [
                    'CurrencyList',
                    'StartAt',
                    [
                        'attribute' => 'approve',
                        'format' => 'raw',
                        'value' => $vacation->isApproved() ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>',
                        'widgetOptions' => [
                            'pluginOptions' => [
                                'onText' => 'Yes',
                                'offText' => 'No',
                            ],
                        ],
                    ],
                ],
            ]); ?>
        <?php endforeach; ?>
    </div>

</div>
