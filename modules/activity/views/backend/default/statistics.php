<?php
use yii\helpers\Html;
use \yii2fullcalendar\yii2fullcalendar;
use \yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\web\View;
use common\models\User;
use modules\project\models\Project;
use modules\vacation\models\Vacation;
use yii\helpers\Json;

$fieldOptions = [
    'options' => ['class' => 'col-lg-5 pull-left'],
    'template' => '{label}<div class="col-md-9 col-sm-9">{input}{hint}{error}</div>',
    'labelOptions' => ['class' => 'control-label col-sm-3 col-md-3'],
];

$this->title = Yii::t('app', 'Statistics');
?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="row">
    <div class="activity-calendar col-lg-8 col-md-12 col-sm-12 col-xs-12">

        <div class="activity-search <?= !Yii::$app->user->identity->isAdmin() ? 'hidden' : '' ?>">

            <div class="media-form alert alert-search">

                <?php $form = ActiveForm::begin([
                    'action' => ['report'],
                    'method' => 'get',
                    'enableClientValidation' => false,
                    'options' => ['id' => 'search-activity'],
                ]); ?>

                <?= $form->field($searchModel, 'user_id', array_merge($fieldOptions, ['inputOptions' => ['class' => 'form-control', 'id' => 'user_id']]))->dropdownList(User::getList()) ?>

                <span class="hidden">
                    <?= $form->field($searchModel, 'date', $fieldOptions) ?>
                </span>

                <?php ActiveForm::end(); ?>

                <div class="clear"></div>

            </div>

        </div>

        <?= yii2fullcalendar::widget(array(
            'id' => 'calendar',
            'clientOptions' => [
                'firstDay' => 1,
                'header' => ['right' => ''],
                'dayClick' => new JsExpression('function(date) { ActivityHelper.openActivities(date); }'),
                'viewRender' => new JsExpression('function(view, element) { ActivityHelper.renderView(view, element); }'),
            ],
        )); ?>

    </div>
</div>

<br /><br />