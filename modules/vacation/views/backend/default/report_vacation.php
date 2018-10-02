<?php
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use modules\payment\models\Payment;

$this->title = Yii::t('app', 'Report Vacation');
?>
<div id="massage"></div>
<h1><?= Html::encode($this->title) ?></h1>
<?php
$script = <<< JS
    var reportVacation=true;
JS;
$this->registerJs($script, View::POS_END);
?>
<div class="row">


    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
            <div class="payment-form col-lg-5">

                <?php $form = ActiveForm::begin(); ?>
                <div class="col-sm-12">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'year',[
                            'template' => '{label}<div class="col-sm-6">{input}{error}{hint}</div>',
                            'labelOptions' => ['class' => 'control-label  col-sm-6 '],
                        ])->dropDownList(Payment::listRandom(),['value'=>$year])?>
                    </div>
                        <div class="pull-left">
                            <?= Html::a(Yii::t('app', 'Choose'),['generalvacation', 'year' =>$year], ['class' => 'btn btn-success','id'=>'vacation']) ?>
                        </div>
                </div>
                <?php ActiveForm::end(); ?>

                <div class="clearfix"></div>
            </div>
        </div>
        <div class="col-sm-12">
                <div id="reportVacation" class="handsontable1"></div>
        </div>


        <div class="clearfix"></div>
    </div>



</div>