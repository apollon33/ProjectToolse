<?php
use yii\helpers\Html;
use yii\web\View;
use common\models\User;
use yii\widgets\ActiveForm;
use yii\helpers\Json;
use modules\payment\models\Payment;
use common\helpers\Toolbar;
use backend\assets\VacationAssets;

VacationAssets::register($this);

$this->title = Yii::t('app', 'General Report');
?>
<div id="massage"></div>
<h1><?= Html::encode($this->title) ?></h1>
<?php
$date=Json::encode($reportcart);
$script = <<< JS
    var reportCart=true;
JS;
$this->registerJs($script, View::POS_END);
?>
<div class="row">


    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

        <div class="col-lg-6 col-md-7">

            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal','id' => 'reportCart']]); ?>
            <div class="col-sm-10">
                <div class="col-sm-12">
                    <?= $form->field($model, 'created_by',[
                        'template' => '{label}<div class="col-sm-9">{input}{error}{hint}</div>',
                        'labelOptions' => ['class' => 'control-label  col-sm-3 '],
                    ])->dropDownList(User::getListManager(),[
                        'value'=>$created_by,
                        /*'disabled'=> ( Yii::$app->user->identity->role_id==User::TYPE_SUPER_ADMIN  ?
                            false
                            :true)*/])?>
                </div>
                <div class="col-sm-12">
                <?= $form->field($model, 'user_id',[
                    'template' => '{label}<div class="col-sm-9">{input}{error}{hint}</div>',
                    'labelOptions' => ['class' => 'control-label  col-sm-3 '],
                ])->dropDownList( User::getListDaveloper(),['prompt' => Yii::t('app', 'All User'),'value'=>(!empty($id)  ?  $id : '')])?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'month',[
                        'template' => '{label}<div class="col-sm-6">{input}{error}{hint}</div>',
                        'labelOptions' => ['class' => 'control-label  col-sm-6 '],
                    ])->dropDownList(Toolbar::month(),['value'=>date('n',strtotime($year.'-'.$month.'-01'))])?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'year',[
                        'template' => '{label}<div class="col-sm-6">{input}{error}{hint}</div>',
                        'labelOptions' => ['class' => 'control-label  col-sm-6 '],
                    ])->dropDownList(Payment::listRandom(),['value'=>$year])?>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="pull-left">
                    <?= Html::a('Choose',['reportcart','year' =>$year, 'month' => date('n',strtotime($year.'-'.$month.'-01')),'created_by' =>$created_by], ['class' => 'btn btn-success','id'=>'user_id-reportCart']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>

            <div class="clearfix"></div>
        </div>

        <div class="clearfix"></div>
    </div>
    <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
        <div class="form-group">
            <label id="month" class="col-sm-2 control-label"><?= Html::encode($month)  ?></label>
            <label id="year" class="col-sm-2 control-label"><?=  Html::encode($year)  ?></label>
        </div>
    </div>
    <?php
    foreach ($reportcart as $item){
        $project=$item['project']; ?>
        <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
            <div class="form-group">
                <label id="month" class="col-sm-2 control-label"><?= User::getList()[$item['user_id']]  ?></label>
            </div>
        </div>
        <?php  if(!empty(Json::decode($project))):?>
            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">


                <div class="col-sm-12">
                    <div id="example-<?=$item['user_id']?>" class="handsontable1"></div>
                </div>
                <div class="col-sm-12">

                    <form class="form-inline" style="padding: 10px;">
                        <div class="form-group">
                            <label for="exampleInputName2"><?= Yii::t('app', 'All Time') ?></label>
                            <input type="text" class="form-control" id="all-reportcart-<?= $item['user_id']?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail2"><?= Yii::t('app', 'All Time Weeking') ?></label>
                            <input type="text" class="form-control" id="weekind-all-reportcart-<?= $item['user_id']?>" disabled>
                        </div>
                    </form>
                </div>
            </div>
        <?php else:?>
            <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
                <div style="font-size: 14pt;color: #e91e1e;"><?= Yii::t('app', 'No project assigned') ?></div>
            </div>
        <?php endif;}?>



</div>