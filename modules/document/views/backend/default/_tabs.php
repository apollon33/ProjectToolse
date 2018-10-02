<?php
use yii\bootstrap\Tabs;
use common\widgets\Gallery;

$node = $params['node'];
$update = $params['update'];
$isPermission = $params['isPermission'];

?>
<br/>
<?= Tabs::widget([
    'items' => [
        [
            'label' => Yii::t('app', 'View'),
            'active' => !$node->isNewRecord,
            'visible' => !$node->isNewRecord,
            'options' => ['id' => 'view'],
            'content' => $node->isNewRecord ? '' : $this->render('_view', ['params' => $params]),
        ],
        [
            'label' => $node->isNewRecord ? Yii::t('app', 'Add New') : Yii::t('app', 'Edit'),
            'active' => $node->isNewRecord,
            'visible' => $update ,
            'options' => ['id' => 'edit'],
            'content' => $this->render('_fields', ['params' => $params]),
        ],
        [
            'label' => Yii::t('app', 'Permission'),
            'visible' => $isPermission,
            'options' => ['id' => 'Rermission'],
            'content' => $this->render('_permission', ['params' => $params]),
        ],
    ],
]); ?>

