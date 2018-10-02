<?php

use yii\helpers\Html;

/* @var yii\web\View $this */
/* @var common\models\User $model */
/* @var string|null $activeTab */

$this->title = Yii::t('app', 'Edit User') . ': ' . $model->username;
?>

<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'activeTab' => $activeTab,
        'dataProviderPosition' => $dataProviderPosition,
        'dataProviderRegistration' => $dataProviderRegistration,
        'dataProviderSalary' => $dataProviderSalary,
        'dataMultipleEMails' => $dataMultipleEMails,
        'multipleEMails' => $multipleEMails,
        'leftVacation' => $leftVacation,
        'listVacation' => $listVacation,
        'modelVacation' => $modelVacation,
        'fullVacation' => $fullVacation,
        'allVacationList' => $allVacationList,
        'permissions' => $permissions,
        'rolePermissions' => $rolePermissions,

    ]) ?>

</div>
