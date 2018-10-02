<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

use modules\buildingprocess\models\ProcessTemplate;

use modules\document\models\Permission;
use common\models\User;


/* @var $this yii\web\View */

$this->title = 'Add Process Instance';
$this->params['breadcrumbs'][] = ['label' => 'Processes Instance', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Modal::begin([
    'id' => 'document-modal',
    'header' => '<h4 class="modal-title">' . Yii::t('app', 'Document') . '</h4>',
    'toggleButton' => [
        'class' => 'document-button hidden',
        'data-target' => '#document-modal',
    ],
    'clientOptions' => false,
]);?>
<div class="document-body">
</div>

<div class="form-horizontal">
    <?= Html::a('Cancel', '#', ['class' => 'btn btn-primary link-popup-close']) ?>
    <?= Html::a('Save', '#',['class' => 'btn btn-success doc-p-i']) ?>
</div>



<?php Modal::end();?>
<div class="building-process-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="building-process-form col-lg-8 alert alert-info">
        <div class="panel panel-default">
        <?php Pjax::begin([
            'id' => 'formSaving',
            'enablePushState' => false,
            'linkSelector' => 'a.pjax-modal-saving',
        ]); ?>
        <?= $this->render('_form_create', [
            'modelPermission' => $modelPermission,
            'processInstance' => $processInstance,
        ]) ?>
        <?php Pjax::end(); ?>
        </div>
    </div>

    <div class="clearfix"></div>


</div>
