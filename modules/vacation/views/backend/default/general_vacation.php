<?php
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use modules\payment\models\Payment;
use common\helpers\Toolbar;
use backend\assets\VacationAssets;

VacationAssets::register($this);

$this->title = Yii::t('app', 'General Report Vacation');
?>
<div class = 'general_vacation_fix_table'>
<div id="massage"></div>
<h1><?= Html::encode($this->title) ?></h1>
<?php
$script = <<< JS
    var calendarVacation=true;
JS;
$this->registerJs($script, View::POS_END);
?>

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

<div class="row">


    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
            <div class="payment-form col-lg-7 col-sm-12 col-md-9">

                <?php $form = ActiveForm::begin(); ?>
                <div class="col-sm-12">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'month',[
                            'template' => '{label}<div class="col-sm-6 col-md-8">{input}{error}{hint}</div>',
                            'labelOptions' => ['class' => 'control-label col-sm-6  col-md-4'],
                        ])->dropDownList(Toolbar::month(),['value'=>date('n',strtotime($year.'-'.$month.'-01'))])?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'year',[
                            'template' => '{label}<div class="col-sm-6  col-md-8">{input}{error}{hint}</div>',
                            'labelOptions' => ['class' => 'control-label  col-sm-6  col-md-4'],
                        ])->dropDownList(Payment::listRandom(),['value'=>$year])?>
                    </div>
                    <div class="col-sm-12">
                        <div class="pull-left">
                            <?= Html::a(Yii::t('app', 'Choose'), ['generalvacation', 'year' =>$year, 'month' => date('n',strtotime($year.'-'.$month.'-01'))], ['class' => 'btn btn-success','id'=>'period-vacation']) ?>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>

                <div class="clearfix"></div>
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
            <ul>
                <li style="color: #64bf4e"><?= Yii::t('app', 'Paid leave')?></li>
                <li style="color: #d61e11"><?= Yii::t('app', 'Not Point')?></li>
                <li style="color: #007dd1"><?= Yii::t('app', 'Sick Leave')?></li>
            </ul>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="form-group">
                <?= Html::label(Html::encode($month), '', ['class' => 'col-sm-2 control-label','id'=>'month']) ?>
                <?= Html::label(Html::encode($year), '', ['class' => 'col-sm-2 control-label','id'=>'year']) ?>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="col-sm-12">
                <div id="tableVacation" class="handsontable1"></div>
            </div>
        </div>


        <div class="clearfix"></div>
    </div>



</div>
</div>