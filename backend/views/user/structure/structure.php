<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\tree\Module;
use yii\bootstrap\Modal;
use modules\document\models\Document;
use modules\position\models\Position;
use modules\department\models\Department;
use common\models\structure\TreeViewQMS;
use common\models\structure\CollectTreeSource;

use common\access\AccessManager;

/* @var $this yii\web\View */

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'IT Master');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-index">
	<div class="row">
		<h1 class="pull-left tree-page-title" style="width:50%;"><?= Html::encode($this->title) ?></h1>
		<div class="pull-right tree-create-buttons">
			<?php Modal::begin([
				'header' => '<h2>' . Yii::t('app', 'New Department') . '</h2>',
				'id' => 'new_department',
				'size' => 'modal-sm',
				'toggleButton' => [
					'label' => '<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Create Department'),
					'class' => !Yii::$app->user->can('department.backend.default:' . AccessManager::CREATE) ? 'hidden' : 'btn btn-success' ,
					'data-target' => '#new_department'
				]
			]) ?>
			<div id="url_content">
				<?= $this->render('_modal_department', ['model' => (new Department())]); ?>
			</div>
			<?php Modal::end(); ?>
			<?php Modal::begin([
				'header' => '<h2>' . Yii::t('app', 'New Position') . '</h2>',
				'id' => 'new_position',
				'size' => 'modal-sm',
				'toggleButton' => [
					'label' => '<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Create Position'),
					'class' => !Yii::$app->user->can('position.backend.default:' . AccessManager::CREATE) ? 'hidden' : 'btn btn-success',
					'data-target' => '#new_position'
				]
			]) ?>
			<div id="url_content">
				<?= $this->render('_modal_position', ['model' => (new Position())]); ?>
			</div>
			<?php Modal::end(); ?>
		</div>
	</div>

	<?= TreeViewQMS::widget([
		'query' => Document::find()->addOrderBy('root, lft'),
		'source' => CollectTreeSource::getTreeSource(),
		'id' => 'treeID',
		'displayValue' => $id,
		'nodeActions' => [
			Module::NODE_MANAGE => Url::to(['/user/user-preview']),
		],
		'nodeView' => '/user/structure/_form_tree_preview',
//		'nodeAddlViews' => [ Module::VIEW_PART_1 => '_field1' ],
		'headingOptions' => ['label' => Yii::t('app', 'IT Master')],
		'emptyNodeMsg' => Yii::t('app', 'Select a tree node to display.'),
		'treeOptions' => ['style' => 'height: 700px'],
		'detailOptions' => ['style' => 'height:740px'],
		'buttonOptions' => ['class' => 'btn btn-success'],
		'rootOptions' => ['class' => 'text-primary', 'label' => Yii::t('app', 'IT Master')],
		'treeWrapperOptions' => ['class' => 'kv-tree-wrapper form-control'],
	]); ?>

</div>