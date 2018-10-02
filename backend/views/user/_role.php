<?php

use yii\helpers\ArrayHelper;
use common\models\User;
use kartik\select2\Select2;

/* @var common\models\User $model */
/* @var yii\widgets\ActiveForm $form */

?>

<?=  $form->field($model, 'roleListName')->widget(Select2::classname(), [
    'data' =>  ArrayHelper::map(User::getListRoles(), 'name', 'name'),
    'options' => ['placeholder' => 'Select a user ...', 'multiple' => true, ''],
    'pluginOptions' => [
        'tokenSeparators' => [',', ' '],
        'maximumInputLength' => 10,
    ],
]); ?>

