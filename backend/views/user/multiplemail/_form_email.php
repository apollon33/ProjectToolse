<?php

use yii\widgets\Pjax;
use common\helpers\Toolbar;

?>
    <div class="panel-body">
    <?=Toolbar::createButtonMultipleEmail(Yii::t('app', 'Add E-Mail'), Yii::$app->request->get('id'))?>
    </div>
<?php Pjax::begin([
    'id' => 'multiEmail',
    'enablePushState' => false,])?>
<?= $this->render('index',[
    'dataProvider' => $dataProvider,
]) ?>

<?php Pjax::end()?>