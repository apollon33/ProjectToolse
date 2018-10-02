<?php
namespace  modules\buildingprocess\models\document;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\tree\TreeView;
use backend\assets\TreeViewAssetDocument;
use kartik\dialog\Dialog;
use yii\bootstrap\BootstrapPluginAsset;
use kartik\tree\Module;
/**
 * Class TreeViewQMS
 *
 * @package modules\user\models\structure
 */
class DocumentViewQMS extends TreeView
{

    public $mainTemplate = <<< HTML
<div class="row">
    <div class="col-sm-6">
        {wrapper}
    </div>
      <div class="col-sm-6">
       {detail}
    </div>
</div>
HTML;
    
    /**
     * Registers the client assets for the widget
     */
    public function registerAssets()
    {
        $view = $this->getView();
        TreeViewAssetDocument::register($view);
        if ($this->_hasBootstrap && $this->autoLoadBsPlugin) {
            BootstrapPluginAsset::register($view);
        }
        Dialog::widget($this->krajeeDialogSettings);
        $this->pluginOptions += [
            'dialogLib' => ArrayHelper::getValue($this->krajeeDialogSettings, 'libName', 'krajeeDialog'),
            'treeId' => $this->treeOptions['id'],
            'detailId' => $this->detailOptions['id'],
            'toolbarId' => $this->toolbarOptions['id'],
            'wrapperId' => $this->treeWrapperOptions['id'],
            'actions' => $this->nodeActions,
            'modelClass' => $this->query->modelClass,
            'formAction' => $this->nodeActions[Module::NODE_SAVE],
            'formOptions' => $this->nodeFormOptions,
            'currUrl' => Yii::$app->request->url,
            'messages' => $this->clientMessages,
            'alertFadeDuration' => $this->alertFadeDuration,
            'enableCache' => ArrayHelper::getValue($this->cacheSettings, 'enableCache', true),
            'cacheTimeout' => ArrayHelper::getValue($this->cacheSettings, 'cacheTimeout', 300000),
            'showTooltips' => $this->showTooltips,
            'isAdmin' => $this->isAdmin,
            'showInactive' => $this->showInactive,
            'softDelete' => $this->softDelete,
            'iconsList' => $this->_iconsList,
            'showFormButtons' => $this->showFormButtons,
            'showIDAttribute' => $this->showIDAttribute,
            'nodeView' => $this->nodeView,
            'nodeAddlViews' => $this->nodeAddlViews,
            'nodeSelected' => $this->_nodeSelected,
            'breadcrumbs' => $this->breadcrumbs,
            'multiple' => $this->multiple,
            'cascadeSelectChildren' => $this->cascadeSelectChildren,
            'allowNewRoots' => $this->allowNewRoots,
            'hideUnmatchedSearchItems' => $this->hideUnmatchedSearchItems,
            'showNameAttribute' => $this->showNameAttribute,
        ];
        $this->pluginOptions['rootKey'] = self::ROOT_KEY;
        $this->registerPlugin('treeview');
    }

    /**
     * Initialize all options & settings for the widget
     */
    public function initOptions()
    {
        if (!$this->_module->treeStructure['treeAttribute']) {
            $this->allowNewRoots = false;
        }
        $this->_nodes = $this->query->all();
        $this->_iconPrefix = $this->fontAwesome ? 'fa fa-' : 'glyphicon glyphicon-';
        $this->_nodeSelected = $this->options['id'] . '-nodesel';
        $this->initSelectedNode();
        $this->nodeFormOptions['id'] = $this->options['id'] . '-nodeform';
        $this->options['data-key'] = $this->displayValue;
        if (empty($this->buttonIconOptions['class'])) {
            $this->buttonIconOptions['class'] = $this->fontAwesome ? 'kv-icon-10' : 'kv-icon-05';
        }
        if (empty($this->options['class'])) {
            $this->options['class'] = 'form-control hide';
        }
        Html::addCssClass($this->headerOptions, 'kv-header-container');
        Html::addCssClass($this->headingOptions, 'kv-heading-container');
        Html::addCssClass($this->toolbarOptions, 'kv-toolbar-container');
        Html::addCssClass($this->footerOptions, 'kv-footer-container');
        $css = ['kv-tree-container'];
        if ($this->showCheckbox) {
            $css[] = 'kv-has-checkbox';
        }
        if (!$this->multiple) {
            $css[] = 'kv-single-select';
        }
        Html::addCssClass($this->treeOptions, $css);
        Html::addCssClass($this->rootOptions, 'kv-tree-root');
        Html::addCssClass($this->nodeToggleOptions, 'kv-node-toggle');
        Html::addCssClass($this->nodeCheckboxOptions, 'kv-node-checkbox');
        Html::addCssClass($this->rootNodeToggleOptions, 'kv-root-node-toggle');
        Html::addCssClass($this->rootNodeCheckboxOptions, 'kv-root-node-checkbox');
        Html::addCssClass($this->detailOptions, 'kv-detail-container');
        Html::addCssClass($this->searchContainerOptions, 'kv-search-container');
        Html::addCssClass($this->searchOptions, 'kv-search-input');
        Html::addCssClass($this->searchClearOptions, 'kv-search-clear');
        Html::addCssClass($this->expandNodeOptions, 'kv-node-expand');
        Html::addCssClass($this->collapseNodeOptions, 'kv-node-collapse');
        Html::addCssClass($this->childNodeIconOptions, 'kv-node-icon');
        Html::addCssClass($this->parentNodeIconOptions, 'kv-node-icon');
        Html::addCssClass($this->childNodeIconOptions, 'kv-icon-child');
        Html::addCssClass($this->parentNodeIconOptions, 'kv-icon-parent');
        if (empty($this->searchClearOptions['title'])) {
            $this->searchClearOptions['title'] = Yii::t('kvtree', 'Clear search results');
        }
        Html::addCssClass($this->buttonGroupOptions, 'btn-group');
        $this->treeWrapperOptions['id'] = $this->options['id'] . '-wrapper';
        $this->treeOptions['id'] = $this->options['id'] . '-tree';
        $this->detailOptions['id'] = $this->options['id'] . '-detail';
        $this->toolbarOptions['id'] = $this->options['id'] . '-toolbar';
        if (!isset($this->searchOptions['placeholder'])) {
            $this->searchOptions['placeholder'] = Yii::t('kvtree', 'Search...');
        }
        $this->toolbarOptions['role'] = 'toolbar';
        $this->buttonGroupOptions['role'] = 'group';
        $this->clientMessages += [
            'invalidCreateNode' => Yii::t('kvtree', 'Cannot create node. Parent node is not saved or is invalid.'),
            'emptyNode' => Yii::t('kvtree', '(new)'),
            'removeNode' => Yii::t('kvtree', 'Are you sure you want to remove this node?'),
            'nodeRemoved' => Yii::t('kvtree', 'The node was removed successfully.'),
            'nodeRemoveError' => Yii::t('kvtree', 'Error while removing the node. Please try again later.'),
            'nodeNewMove' => Yii::t('kvtree', 'Cannot move this node as the node details are not saved yet.'),
            'nodeTop' => Yii::t('kvtree', 'Already at top-most node in the hierarchy.'),
            'nodeBottom' => Yii::t('kvtree', 'Already at bottom-most node in the hierarchy.'),
            'nodeLeft' => Yii::t('kvtree', 'Already at left-most node in the hierarchy.'),
            'nodeRight' => Yii::t('kvtree', 'Already at right-most node in the hierarchy.'),
            'emptyNodeRemoved' => Yii::t('kvtree', 'The untitled node was removed.'),
            'selectNode' => Yii::t('kvtree', 'Select a node by clicking on one of the tree items.'),
        ];
        $defaultToolbar = [];

        if (!$this->allowNewRoots) {
            unset($defaultToolbar[self::BTN_CREATE_ROOT]);
        }
        $this->toolbar = array_replace_recursive($defaultToolbar, $this->toolbar);
        $this->sortToolbar();
        if ($this->defaultChildNodeIcon === null) {
            $this->defaultChildNodeIcon = $this->getNodeIcon(1);
        }
        if ($this->defaultParentNodeIcon === null) {
            $this->defaultParentNodeIcon = $this->getNodeIcon(2);
        }
        if ($this->defaultParentNodeOpenIcon === null) {
            $this->defaultParentNodeOpenIcon = $this->getNodeIcon(3);
        }
        $this->_iconsList = $this->getIconsList();
    }

}