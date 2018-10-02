<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;




$this->title = Yii::t('app', 'Verify Email');

?>

<h1><?= Html::encode($this->title) ?></h1>


<div class="project-form col-lg-8 alert alert-info">

    <?php $form = ActiveForm::begin(); ?>

    <div class="panel panel-default">


        <div class="panel-body">

            <div class="form-group">
                <label class="control-label col-sm-2 col-md-2 col-xs-12"><?= Yii::t('app', 'Root Email')?></label>
                <div class="col-md-10 col-sm-10"> <?= Html::input('text', 'root_email', 'info@demo-itmaster.com', ['class' =>'form-control'],['value'=>'info@demo-itmaster.com']) ?>
                    <div class="text-danger"></div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2 col-md-2 col-xs-12" ><?= Yii::t('app', 'Emails');?></label>
                <div class="col-md-10 col-sm-10">
                    <?= Html::textarea('email', '' ,['class' =>'form-control','rows'=>'8']) ?>
                    <div class="text-danger"></div>
                </div>
            </div>

            <?php if(isset($emails)):?>
                <?php if(!empty($emails)):?>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th><?=Yii::t('app', 'Email')?></th>
                            <th><?=Yii::t('app', 'Message')?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($emails as $item):?>
                            <tr class="<?php if(!empty($item['class'])) echo $item['class'];?>" >
                                <th scope="row"></th>
                                <td> <label class="control-label col-sm-2 col-md-2 col-xs-12"><?php if(isset($item['email'])) echo $item['email'];?></label></td>
                                <td>
                                    <?php if(isset($item['message'])):?>
                                        <?php if($item['class']==='success') :?>
                                            <div class="col-xs-12"> <?php if(isset($item['message'][18])) echo $item['message'][18];?></div>
                                        <?php else:?>
                                            <div class="col-xs-12"> <?php if(isset($item['message'][11])) echo $item['message'][11];?></div>
                                            <div class="col-xs-12"> <?php if(isset($item['message'][18])) echo $item['message'][18];?></div>
                                        <?php endif;?>
                                    <?php endif;?>

                                </td>
                            </tr>
                    <?php endforeach;?>
                        </tbody>
                    </table>
                <?php else:?>
                    <div class="col-xs-12" style="color:red;"><h2><?php echo Yii::t('app', 'There is not one valid Email');?></h2></div>
                <?php endif;?>
            <?php endif;?>
        </div>

    </div>

    <div class="pull-right">
        <?= Html::submitButton(Yii::t('app', 'Check'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="clearfix"></div>



</div>



