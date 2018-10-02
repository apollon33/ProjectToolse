<?php
use yii\helpers\Html;
use \yii2fullcalendar\yii2fullcalendar;
use yii\helpers\Url;
use \yii\web\JsExpression;
use yii\bootstrap\Modal;
use yii\web\View;
use yii\widgets\Pjax;
use common\models\User;
use backend\assets\CalendarAsset;

$this->title = Yii::t('app', 'Calendar');

CalendarAsset::register($this);

?>

<h1><?= Html::encode($this->title) ?></h1>



<?php
Modal::begin([
    'id' => 'calendar-modal',
    'header' => '<h4 class="modal-title">'.Yii::t('app', 'Event') .'</h4>',
    'toggleButton' => [
        'class' => 'calendar-button hidden',
        'data-target' => '#calendar-modal',
    ],
    'clientOptions' => false,
]); ?>
<?php Pjax::begin([
    'id' => 'eventModel',
    'enablePushState' => false,
]); ?>

<?php Pjax::end(); ?>

<?php Modal::end(); ?>

<div class="row">
    <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
        <div class="pull-left">
            <button type="submit" class="prev-button" ><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span></button>
            <button type="submit" class="next-button" ><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></button>
            <button type="submit" class="buttom-event today-button" ><?=  Yii::$app->formatter->asDate(time(),'php:d.m.Y')  ?></button>
        </div>
        <div class="pull-right">
            <button type="submit" class="buttom-event month-button state-active" ><?= Yii::t('app','month')?></button>
            <button type="submit" class="buttom-event basicWeek-button" ><?= Yii::t('app','week')?></button>
        </div>
        <?= yii2fullcalendar::widget(
                [
                    'id' => 'actionCelendar',
                    'header' => [
                        'left' => 'prev,next,today',
                        'center' => 'title',
                        'right' => 'month,basicWeek'
                    ],

                    'clientOptions' => [
                        'firstDay'=>1,
                        'eventClick' => new JsExpression('function(event, element) {ActionCalendarHelper.id=event.id;}'),
                        'dayRender' => new JsExpression('function (date, element) {CallendarHelper.setAllHolidayConfig(date, element);}'),
                    ],
                    'ajaxEvents' => Url::to(['withdrawal-event']),
                    'options' => [
                        'timeFormat' => '  '
                    ],
                ]
        );?>
    </div>
    <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
        <div class="col-md-12">
            <?= $this->render('_form_event', [
                'model' => $model,
            ]) ?>
        </div>
        <div class="col-md-12">
            <?= $this->render('_search_holiday', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]) ?>
        </div>

    </div>

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="height: 200px; margin-top: 30px;">

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="background:#fae7de; width: 60px;height: 60px;"></div>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                Setting the header options to false will display no header. An object can be supplied with properties left, center, and right. These properties contain strings with comma/space separated values.
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="background:#fcf0b1; width: 60px;height: 60px;"></div>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                Setting the header options to false will display no header. An object can be supplied with properties left, center, and right. These properties contain strings with comma/space separated values.
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="background:#8ae6d8; width: 60px;height: 60px;"></div>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                Setting the header options to false will display no header. An object can be supplied with properties left, center, and right. These properties contain strings with comma/space separated values.
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="background:#bbe7fa; width: 60px;height: 60px;"></div>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                Setting the header options to false will display no header. An object can be supplied with properties left, center, and right. These properties contain strings with comma/space separated values.
            </div>
        </div>

    </div>

</div>



