<?php
use yii\helpers\Html;
use modules\holiday\models\Holiday;
use kartik\datetime\DateTimePicker;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\web\View;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model modules\actioncalendar\models\Event */
/* @var $form yii\widgets\ActiveForm */

$userAgeLimit  = 100;
$datePickerRange = (date('Y') - $userAgeLimit) . ':' . date('Y');

?>

<?php Pjax::begin([
    'id' => 'eventForm',
    'enablePushState' => false,
]); ?>


<?php $form = ActiveForm::begin([
    'action' => Url::to(['create']),
    'options' => [
        'enctype' => 'multipart/form-data',
        'class' => 'form-horizontal',
        'id' => 'eventCreate',
        'data' => ['pjax' => true] ]]); ?>



<?= $form->field($model, 'name',[
    'template' => '<div class="col-md-10 col-sm-10 col-md-offset-2">{input}{error}{hint}</div>'
])->textInput(['maxlength' => true,'placeholder' => $model->getAttributeLabel( 'name' )])->label(false) ?>

<?= $form->field($model, 'type',[
    'template' => '<div class="col-md-10 col-sm-10 col-md-offset-2">{input}{error}{hint}</div>'
])->dropDownList(Holiday::getTypeHoliday(),['prompt' => Yii::t('app','Category')])->label(false) ?>


            <?=  $form->field($model, 'start_at',[
                                                    'template' => '<div class="col-md-10 col-sm-10 col-md-offset-2">{input}{error}{hint}</div>'
                                                ])->widget(DateTimePicker::className(),[
                'options' => [
                    'placeholder' => $model->getAttributeLabel( 'start_at' ),
                    'value'=>(!empty($model->start_at)  ?  date('Y-m-d H:i',$model->start_at): '' ),
                ],
                'pluginOptions' => ['autoclose' => true],

            ])->label(false); ?>

            <?=  $form->field($model, 'end_at',[
                                                  'template' => '<div class="col-md-10 col-sm-10 col-md-offset-2">{input}{error}{hint}</div>'
                                              ])->widget(DateTimePicker::className(),[
                'options' => [
                    'placeholder' => $model->getAttributeLabel( 'end_at' ),
                    'value'=>(!empty($model->end_at) ?  date('Y-m-d H:i',$model->end_at):'' ),
                ],
                'pluginOptions' => ['autoclose' => true],

            ])->label(false); ?>



<div class="pull-right">
    <?= Html::submitButton( 'Offer event' , [
        'class' => 'buttom-event',
        'data-confirm' => Yii::t('app', 'Event will be added after verification!')
    ]) ?>
</div>

<?php ActiveForm::end(); ?>

<div class="clearfix"></div>
<?php Pjax::end(); ?>
