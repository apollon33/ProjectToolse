<?php

use kartik\color\ColorInput;
use modules\buildingprocess\models\Discussion;
/* @var common\models\User $model */
/* @var yii\widgets\ActiveForm $form */
?>
<?= $form->field($model, 'color')->widget(ColorInput::classname(), [
    'options' => [
        'placeholder' => 'Select color ...',
        'value' => $model->color ? $model->color : Discussion::generateRandomColor(),
    ],
]);
?>
