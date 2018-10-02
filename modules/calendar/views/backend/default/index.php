<?php
use yii\helpers\Html;
use \yii2fullcalendar\yii2fullcalendar;
use yii\helpers\Url;
use \yii\web\JsExpression;
use common\models\User;
use backend\assets\CalendarAsset;
$this->title = Yii::t('app', 'Calendar View');

CalendarAsset::register($this);

?>

<h1><?= Html::encode($this->title) ?></h1>
        <?= $this->render('_form')
        ?>
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
                    'id' => 'calendar',
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
                                        CallendarHelper.dropEventEnd(event);}"),
                        'eventClick'=>new JsExpression('function(event, element) {CallendarHelper.id=event.id;}'),
                        "eventResize"  => new JsExpression("function(event, jsEvent, ui, view) {
                                        CallendarHelper.dropEventEnd(event);}"),
                        'dayRender'=>new JsExpression('function (date, element) {CallendarHelper.setAllHolidayConfig(date, element);}'),
                    ],
                ] :
                [
                    'id' => 'calendar',
                    'options' => ['enctype' => 'multipart/form-data'],
                    'clientOptions' => [
                        'firstDay'=>1,
                        'dayRender'=>new JsExpression('function (date, element) {CallendarHelper.setAllHolidayConfig(date, element);}')
                    ],
                ]
        );?>
    </div>
    <?php if(Yii::$app->user->identity->isAllowedToViewStage()):?>
    <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12 checkboxs">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 newTask">
            <?= Html::a('<i class="glyphicon glyphicon-plus"></i>'.
                Yii::t('app', 'New Task'),
                Url::to(['create']),
                ['class' => 'btn btn-info center_buttom btn-lg pjax-modal-campaign newProject']
            ) ?>
        </div>
        <div class="col-lg-12 col-md-6 col-sm-6 col-xs-6">
            <h4><?= Yii::t('app', 'Employee')?></h4>
            <div class="menuCalendar">
                <?php foreach ($user as $value):?>
                    <div class="checkbox user " >
                        <label>
                            <?= Html::checkbox('', false, ['label' => User::getList()[$value['id']],'class'=>'user','value'=>$value['id']]);?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="checkbox" >
                <label>
                    <?= Html::checkbox('', false, ['label' => Yii::t('app', 'Select All'),'class'=>'select_all','id'=>'users']);?>
                </label>
            </div>
        </div>
        <?php if(count($project)):?>
        <div class="col-lg-12 col-md-6 col-sm-6 col-xs-6" >
            <h4><?= Yii::t('app', 'Project')?></h4>
            <div class="menuCalendar">
                <?php foreach ($project as $value):?>
                    <div class="checkbox project " >
                        <label>
                            <?= Html::checkbox('', false, ['label' => \modules\project\models\Project::getList()[$value['id']],'class'=>'project','value'=>$value['id']]);?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="checkbox" >
                <label>
                    <?= Html::checkbox('', false, ['label' => Yii::t('app', 'Select All'),'class'=>'select_all']);?>
                </label>
            </div>
        </div>
        <?php endif;?>

    </div>
    <?php endif; ?>
</div>


