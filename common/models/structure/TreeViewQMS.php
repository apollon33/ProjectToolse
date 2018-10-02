<?php
namespace common\models\structure;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\tree\TreeView;
use kartik\tree\Module;

/**
 * Class TreeViewQMS
 * @package modules\user\models\structure
 */
class TreeViewQMS extends TreeView
{
	public $source = null;
	public $wrapperTemplate = "{header}\n{tree}";

	/**
	 * @inheritdoc
	 */
	public static function begin($config = [])
	{
		$config = self::getConfig($config);
		return parent::begin($config);
	}

	/**
	 * @inheritdoc
	 */
	public static function widget($config = [])
	{
		$config = self::getConfig($config);
		return parent::widget($config);
	}

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		$this->initTreeView();
		parent::init();
		$this->initOptions();
		$this->registerAssets();
	}

	/**
	 * @inheritdoc
	 */
	public function run()
	{
		echo $this->renderWidget();
	}

	/**
	 * Renders the tree wrapper container
	 * @return string
	 */
	public function renderWrapper()
	{
		$content = strtr(
			$this->wrapperTemplate, [
				'{header}' => $this->renderHeader(),
				'{tree}' => $this->renderTree(),
			]
		);
		return Html::tag('div', $content, $this->treeWrapperOptions);
	}

	/**
	 * Initialize all options & settings for the widget
	 */
	public function initOptions()
	{
		if (!$this->_module->treeStructure['treeAttribute']) {
			$this->allowNewRoots = false;
		}
		if (!empty($this->source)) {
			$this->_nodes = $this->source;
		} else {
			$this->_nodes = $this->query->all();
		}
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
		$defaultToolbar = [
			self::BTN_CREATE => [
				'icon' => 'plus',
				'alwaysDisabled' => false, // set this property to `true` to force disable the button always
				'options' => ['title' => Yii::t('kvtree', 'Add new'), 'disabled' => true],
			],
			self::BTN_CREATE_ROOT => [
				'icon' => $this->fontAwesome ? 'tree' : 'tree-conifer',
				'options' => ['title' => Yii::t('kvtree', 'Add new root')],
			],
			self::BTN_REMOVE => [
				'icon' => 'trash',
				'options' => ['title' => Yii::t('kvtree', 'Delete'), 'disabled' => true],
			],
			self::BTN_SEPARATOR,
			self::BTN_MOVE_UP => [
				'icon' => 'arrow-up',
				'options' => ['title' => Yii::t('kvtree', 'Move Up'), 'disabled' => true],
			],
			self::BTN_MOVE_DOWN => [
				'icon' => 'arrow-down',
				'options' => ['title' => Yii::t('kvtree', 'Move Down'), 'disabled' => true],
			],
			self::BTN_MOVE_LEFT => [
				'icon' => 'arrow-left',
				'options' => ['title' => Yii::t('kvtree', 'Move Left'), 'disabled' => true],
			],
			self::BTN_MOVE_RIGHT => [
				'icon' => 'arrow-right',
				'options' => ['title' => Yii::t('kvtree', 'Move Right'), 'disabled' => true],
			],
			self::BTN_SEPARATOR,
			self::BTN_REFRESH => [
				'icon' => 'refresh',
				'options' => ['title' => Yii::t('kvtree', 'Refresh')],
				'url' => Yii::$app->request->url,
			],
		];
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

	/**
	 * @return string
	 */
	public function renderTree()
	{
		$structure = $this->_module->treeStructure + $this->_module->dataStructure;
		extract($structure);
		$nodeDepth = $currDepth = $counter = 0;
		$out = Html::beginTag('ul', ['class' => 'kv-tree']) . "\n";
		foreach ($this->_nodes as $node) {
			/**
			 * @var Tree $node
			 */
			if (!$this->isAdmin && !$node->isVisible() || !$this->showInactive && !$node->isActive()) {
				continue;
			}
			/** @noinspection PhpUndefinedVariableInspection */
			$nodeDepth = $node->$depthAttribute;  // TODO =lvl
			/** @noinspection PhpUndefinedVariableInspection */
//			$nodeLeft = $node->$leftAttribute;    // TODO =lft
			/** @noinspection PhpUndefinedVariableInspection */
//			$nodeRight = $node->$rightAttribute;  // TODO =rgt
			/** @noinspection PhpUndefinedVariableInspection */
			$nodeKey = $node->$keyAttribute;      // TODO =id
			/** @noinspection PhpUndefinedVariableInspection */
			$nodeName = $node->$nameAttribute;
			/** @noinspection PhpUndefinedVariableInspection */
			$nodeIcon = $node->$iconAttribute;
			/** @noinspection PhpUndefinedVariableInspection */
			$nodeIconType = $node->$iconTypeAttribute;

//			$isChild = ($nodeRight == $nodeLeft + 1);
			$isChild = $node->child;
			$indicators = '';

			if (isset($this->nodeLabel)) {
				$label = $this->nodeLabel;
				$nodeName = is_callable($label) ? $label($node) :
					(is_array($label) ? ArrayHelper::getValue($label, $nodeKey, $nodeName) : $nodeName);
			}
			if ($nodeDepth == $currDepth) {
				if ($counter > 0) {
					$out .= "</li>\n";
				}
			} elseif ($nodeDepth > $currDepth) {
				$out .= Html::beginTag('ul') . "\n";
				$currDepth = $currDepth + ($nodeDepth - $currDepth);
			} elseif ($nodeDepth < $currDepth) {
				$out .= str_repeat("</li>\n</ul>", $currDepth - $nodeDepth) . "</li>\n";
				$currDepth = $currDepth - ($currDepth - $nodeDepth);
			}
			if (trim($indicators) == null) {
				$indicators = '&nbsp;';
			}
			$nodeOptions = [
				'data-key' => $nodeKey,
//				'data-lft' => $nodeLeft,
//				'data-rgt' => $nodeRight,
				'data-lvl' => $nodeDepth,
				'data-readonly' => static::parseBool($node->isReadonly()),
				'data-movable-u' => static::parseBool($node->isMovable('u')),
				'data-movable-d' => static::parseBool($node->isMovable('d')),
				'data-movable-l' => static::parseBool($node->isMovable('l')),
				'data-movable-r' => static::parseBool($node->isMovable('r')),
				'data-removable' => static::parseBool($node->isRemovable()),
				'data-removable-all' => static::parseBool($node->isRemovableAll()),
			];

			$css = [];
			if (!$isChild) {
				$css[] = 'kv-parent ';
			}
			if (!$node->isVisible() && $this->isAdmin) {
				$css[] = 'kv-invisible';
			}
			if ($this->showCheckbox && $node->isSelected()) {
				$css[] = 'kv-selected ';
			}
			if ($node->isCollapsed()) {
				$css[] = 'kv-collapsed ';
			}
			if ($node->isDisabled()) {
				$css[] = 'kv-disabled ';
			}
			if (!$node->isActive()) {
				$css[] = 'kv-inactive ';
			}
			$indicators .= $this->renderToggleIconContainer(false) . "\n";
			$indicators .= $this->showCheckbox ? $this->renderCheckboxIconContainer(false) . "\n" : '';
			if (!empty($css)) {
				Html::addCssClass($nodeOptions, $css);
			}
			$out .= Html::beginTag('li', $nodeOptions) . "\n" .
				Html::beginTag('div', ['tabindex' => -1, 'class' => 'kv-tree-list']) . "\n" .
				Html::beginTag('div', ['class' => 'kv-node-indicators']) . "\n" .
				$indicators . "\n" .
				'</div>' . "\n" .
				Html::beginTag('div', ['tabindex' => -1, 'class' => 'kv-node-detail']) . "\n" .
				$this->renderNodeIcon($nodeIcon, $nodeIconType, $isChild) . "\n" .
				Html::tag('span', $nodeName, ['class' => 'kv-node-label']) . "\n" .
				'</div>' . "\n" .
				'</div>' . "\n";
			++$counter;
		}
		$out .= str_repeat("</li>\n</ul>", $nodeDepth) . "</li>\n";
		$out .= "</ul>\n";
		return Html::tag('div', $this->renderRoot() . $out, $this->treeOptions);
	}

    /**
     * Renders the markup for the detail form to edit/view the selected tree node
     *
     * @return string
     */
    public function renderDetail()
    {
        /**
         * @var Tree $modelClass
         * @var Tree $node
         */
//        $modelClass = $this->query->modelClass;
//        $node = $this->displayValue ? $modelClass::findOne($this->displayValue) : null;
        $tempId = $this->displayValue;
        if (!empty($this->displayValue)) {

            $partOfView = explode('-',$this->displayValue);
            $this->displayValue = $partOfView[0];
            $this->nodeView = '@modules/user/views/backend/default/_'.$partOfView[0].'Preview';
            $tempId = $partOfView[1];
        }
        $modelClass = TreeFabric::getClassNameByKey($this->displayValue);
        $node = $this->displayValue ? $modelClass::findOne($tempId) : null;
        $msg = null;
        if (empty($node)) {
            $msg = Html::tag('div', $this->emptyNodeMsg, $this->emptyNodeMsgOptions);
            $node = new $modelClass;
        }
//        $iconTypeAttribute = ArrayHelper::getValue($this->_module->dataStructure, 'iconTypeAttribute', 'icon_type');
//        if ($this->_iconsList !== false) {
//            $node->$iconTypeAttribute = ArrayHelper::getValue($this->iconEditSettings, 'type', self::ICON_CSS);
//        }
        $params = $this->_module->treeStructure + $this->_module->dataStructure + [
                'node' => $node,
                'action' => $this->nodeActions[Module::NODE_SAVE],
                'formOptions' => $this->nodeFormOptions,
                'modelClass' => $modelClass,
                'currUrl' => Yii::$app->request->url,
                'isAdmin' => $this->isAdmin,
                'iconsList' => $this->_iconsList,
                'softDelete' => $this->softDelete,
                'allowNewRoots' => $this->allowNewRoots,
                'showFormButtons' => $this->showFormButtons,
                'showIDAttribute' => $this->showIDAttribute,
                'nodeView' => $this->nodeView,
                'nodeAddlViews' => $this->nodeAddlViews,
                'nodeSelected' => $this->_nodeSelected,
                'breadcrumbs' => $this->breadcrumbs,
                'noNodesMessage' => $msg,
            ];
        $content = $this->render($this->nodeView, ['params' => $params]);
        return Html::tag('div', $content, $this->detailOptions);
    }
}