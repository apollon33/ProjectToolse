<?php
use yii\helpers\Html;
use \yii2fullcalendar\yii2fullcalendar;
use \yii\web\JsExpression;
use yii\helpers\Url;
use modules\vacation\models\Vacation;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use backend\assets\{CalendarAsset, VacationAssets};

$this->title = Yii::t('app', 'Calendar Vacation');


CalendarAsset::register($this);
VacationAssets::register($this);

?>


<h1><?= Html::encode($this->title) ?></h1>
<?php
Modal::begin([
    'id' => 'vacation-modal',
    'header' => '<h4 class="modal-title">'.Yii::t('app', 'Vacation') .'</h4>',
    'toggleButton' => [
        'class' => 'vacation-button hidden',
        'data-target' => '#vacation-modal',
    ],
    'clientOptions' => false,
]); ?>

<?php Pjax::begin([
    'id' => 'formVacation',
    'enablePushState' => false,
    'linkSelector' => 'a.pjax-modal-vacation'
]); ?>

<?php Pjax::end(); ?>

<?php Modal::end(); ?>


<div id="example6" class="handsontable htColumnHeaders"></div>
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
                Yii::$app->user->identity->isAllowedToViewStage() ?
                    [
                        'id' => 'vacationCalendar',
                        'header' => [
                            'left' => 'prev,next,today',
                            'center' => 'title',
                            'right' => 'month,basicWeek'
                        ],
                        'options' => ['enctype' => 'multipart/form-data'],
                        'clientOptions' => [
                            'firstDay'=>1,
                            "editable"   => true,
                            "droppable"  => true,
                            "eventDrop"  => new JsExpression("function(event, delta, revertFunc) {
                                        CallendarVacationHelper.dropEventEnd(event);}"),
                            'eventClick'=>new JsExpression('function(event, element) {CallendarVacationHelper.id=event.id;}'),
                            "eventResize"  => new JsExpression("function(event, jsEvent, ui, view) {
                                        CallendarVacationHelper.dropEventEnd(event);}"),
                            'dayRender'=>new JsExpression('function (date, element) {CallendarHelper.setAllHolidayConfig(date, element);}')
                        ],
                    ] :
                    [
                        'id' => 'calendar',
                        'header' => [
                            'left' => 'prev,next,today',
                            'center' => 'title',
                            'right' => 'month,basicWeek'
                        ],
                        'options' => ['enctype' => 'multipart/form-data'],
                        'clientOptions' => [
                            'firstDay'=>1,
                            'dayRender'=>new JsExpression('function (date, element) {CallendarHelper.setAllHolidayConfig(date, element);}')
                        ],
                    ]);?>
        </div>
    <?php if(Yii::$app->user->identity->isAllowedToViewStage()):?>
        <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <?= Html::a('<i class="glyphicon glyphicon-plus"></i>'.
                    Yii::t('app', 'Add Vacation'),
                    Url::to(['create']),
                    ['class' => 'btn btn-info center_buttom btn-lg pjax-modal-vacation newVacarion']
                ) ?>
            </div>
            <div class="col-lg-12 col-md-6 col-sm-6 col-xs-6">
                <h4><?= Yii::t('app', 'Employee')?></h4>
                <div class="menuCalendar">
                    <?php foreach ($user as $value):?>
                        <div class="checkbox user " >
                            <label id="label_<?=$value['id']?>">
                                <input type="checkbox" class="user" value="<?=$value['id']?>"> <?=$value['last_name'].' '.$value['first_name'].' '.$value['middle_name']?>
                                (<span id="tariff" style="color: #64bf4e"><?= Vacation::typeVacation($value['id'],Vacation::TYPE_TARIFF) ?></span>/
                                <span id="not-point" style="color: #d61e11"><?= Vacation::typeVacation($value['id'],Vacation::TYPE_NOT_PAID) ?></span>/
                                <span id="sick-leave" style="color: #007dd1"><?= Vacation::typeVacation($value['id'],Vacation::TYPE_SICK_LEAVE) ?></span>)
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="checkbox" >
                    <label>
                        <?= Html::checkbox('', false, ['label' => Yii::t('app', 'Select All'),'class'=>'choose-all','id'=>'users']);?>
                    </label>
                </div>
                <div class="col-sm-12">
                    <?= Html::submitButton(Yii::t('app', 'Filter'), ['class' => 'btn btn-primary center_buttom','id'=>'filter']) ?>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <ul>
                    <li style="color: #64bf4e"><?= Yii::t('app', 'Paid leave')?></li>
                    <li style="color: #d61e11"><?= Yii::t('app', 'Not Point')?></li>
                    <li style="color: #007dd1"><?= Yii::t('app', 'Sick Leave')?></li>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</div>



