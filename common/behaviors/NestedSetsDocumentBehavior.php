<?php

namespace common\behaviors;

use creocoder\nestedsets\NestedSetsBehavior;
use yii\db\Exception;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;


class NestedSetsDocumentBehavior extends NestedSetsBehavior
{

    /**
     * @var string
     */
    public $rootAttribute = 'root';

    /**
     * @var string
     */
    public $orderAttribute = 'order';

    /**
     * Creates a node as the first child of the target node if the active
     * record is new or moves it as the first child of the target node.
     * @param ActiveRecord $node
     * @param boolean $runValidation
     * @param array $attributes
     * @return boolean
     */
    public function prependTo($node, $runValidation = true, $attributes = null)
    {
        $this->owner->order = $node->order;
        return parent::prependTo($node, $runValidation, $attributes);
    }

    /**
     * Creates a node as the last child of the target node if the active
     * record is new or moves it as the last child of the target node.
     * @param ActiveRecord $node
     * @param boolean $runValidation
     * @param array $attributes
     * @return boolean
     */
    public function appendTo($node, $runValidation = true, $attributes = null)
    {
        $this->owner->order = $node->order;
        return parent::prependTo($node, $runValidation, $attributes);
    }

    /**
     * @throws Exception
     */
    public function afterInsert()
    {
        if ($this->operation === self::OPERATION_MAKE_ROOT && $this->treeAttribute !== false) {
            $this->owner->order = $this->owner->getActiveValidators($this->rootAttribute);
            $this->owner->setAttribute($this->treeAttribute, $this->owner->getPrimaryKey());
            $primaryKey = $this->owner->primaryKey();

            if (!isset($primaryKey[0])) {
                throw new Exception('"' . get_class($this->owner) . '" must have a primary key.');
            }

            $this->owner->updateAll(
                [
                    $this->treeAttribute => $this->owner->getAttribute($this->treeAttribute),
                    $this->orderAttribute => $this->appointOrder()
                ],
                [$primaryKey[0] => $this->owner->getAttribute($this->treeAttribute)]
            );
        }

        $this->operation = null;
        $this->node = null;
    }

    /**
     * @throws Exception
     */
    public function beforeUpdate()
    {
        if ($this->node !== null && !$this->node->getIsNewRecord()) {
            $this->node->refresh();
        }

        switch ($this->operation) {
            case self::OPERATION_MAKE_ROOT:
                if ($this->treeAttribute === false) {
                    throw new Exception('Can not move a node as the root when "treeAttribute" is false.');
                }

                if ($this->owner->isRoot()) {
                    throw new Exception('Can not move the root node as the root.');
                }

                break;
            case self::OPERATION_INSERT_BEFORE:
            case self::OPERATION_INSERT_AFTER:
            case self::OPERATION_PREPEND_TO:
            case self::OPERATION_APPEND_TO:
                if ($this->node->getIsNewRecord()) {
                    throw new Exception('Can not move a node when the target node is new record.');
                }

                if ($this->owner->equals($this->node)) {
                    throw new Exception('Can not move a node when the target node is same.');
                }

                if ($this->node->isChildOf($this->owner)) {
                    throw new Exception('Can not move a node when the target node is child.');
                }
        }
    }

    /**
     * @return void
     */
    public function afterUpdate()
    {
        switch ($this->operation) {
            case self::OPERATION_MAKE_ROOT:
                $this->moveNodeAsRoot();
                break;
            case self::OPERATION_PREPEND_TO:
                $this->moveNode($this->node->getAttribute($this->leftAttribute) + 1, 1);
                break;
            case self::OPERATION_APPEND_TO:
                $this->moveNode($this->node->getAttribute($this->rightAttribute), 1);
                break;
            case self::OPERATION_INSERT_BEFORE:
                if($this->owner->isRoot()) {
                    $this->moveNodeRoot();
                } else {
                    $this->moveNode($this->node->getAttribute($this->leftAttribute), 0);
                }
                break;
            case self::OPERATION_INSERT_AFTER:
                if($this->owner->isRoot()) {
                    $this->moveNodeRoot();
                } else {
                    $this->moveNode($this->node->getAttribute($this->rightAttribute) + 1, 0);
                }
                break;
            default:
                return;
        }

        $this->operation = null;
        $this->node = null;
    }

    /**
     * @return void
     */
    protected function moveNodeRoot()
    {
        $treeValue = $this->owner->getAttribute($this->treeAttribute);
        $orderValue = $this->owner->getAttribute($this->orderAttribute);
        $treeValueNode = $this->node->getAttribute($this->treeAttribute);
        $orderValueNode = $this->node->getAttribute($this->orderAttribute);
        $this->owner->updateAll(
            [$this->orderAttribute => $orderValueNode],
            [$this->treeAttribute => $treeValue]
        );
        $this->owner->updateAll(
            [$this->orderAttribute => $orderValue],
            [$this->treeAttribute => $treeValueNode]
        );
    }

    /**
     * @return void
     */
    protected function moveNodeAsRoot()
    {
        $db = $this->owner->getDb();
        $leftValue = $this->owner->getAttribute($this->leftAttribute);
        $rightValue = $this->owner->getAttribute($this->rightAttribute);
        $depthValue = $this->owner->getAttribute($this->depthAttribute);
        $treeValue = $this->owner->getAttribute($this->treeAttribute);
        $order = $this->owner->getAttribute($this->orderAttribute);
        $leftAttribute = $db->quoteColumnName($this->leftAttribute);
        $rightAttribute = $db->quoteColumnName($this->rightAttribute);
        $depthAttribute = $db->quoteColumnName($this->depthAttribute);
        $orderAttribute = $db->quoteColumnName($this->orderAttribute);
        
        $this->owner->updateAll(
            [$this->orderAttribute => new Expression($orderAttribute . sprintf('%+d', +1))],
            ['>', $this->orderAttribute, $order]
        );

        $this->owner->updateAll(
            [
                $this->leftAttribute => new Expression($leftAttribute . sprintf('%+d', 1 - $leftValue)),
                $this->rightAttribute => new Expression($rightAttribute . sprintf('%+d', 1 - $leftValue)),
                $this->depthAttribute => new Expression($depthAttribute  . sprintf('%+d', -$depthValue)),
                $this->treeAttribute => $this->owner->getPrimaryKey(),
                $this->orderAttribute => $this->reassignOrder(),
            ],
            [
                'and',
                ['>=', $this->leftAttribute, $leftValue],
                ['<=', $this->rightAttribute, $rightValue],
                [
                    $this->treeAttribute => $treeValue,
                ]
            ]
        );

        $this->shiftLeftRightAttribute($rightValue + 1, $leftValue - $rightValue - 1);

    }

    /**
     * @return int
     */
    protected function appointOrder()
    {
        $max = (new Query())
            ->from($this->owner->tableName())
            ->max('`order`');
        return $max + 1;
    }

    /**
     * @return int
     */
    protected function reassignOrder()
    {
        return intval($this->owner->order) + 1;
    }

    /**
     * @param integer $value
     * @param integer $depth
     */
    protected function moveNode($value, $depth)
    {
        $db = $this->owner->getDb();
        $leftValue = $this->owner->getAttribute($this->leftAttribute);
        $rightValue = $this->owner->getAttribute($this->rightAttribute);
        $depthValue = $this->owner->getAttribute($this->depthAttribute);
        $depthAttribute = $db->quoteColumnName($this->depthAttribute);
        $depth = $this->node->getAttribute($this->depthAttribute) - $depthValue + $depth;

        if ($this->treeAttribute === false
            || $this->owner->getAttribute($this->treeAttribute) === $this->node->getAttribute($this->treeAttribute)) {
            $delta = $rightValue - $leftValue + 1;
            $this->shiftLeftRightAttribute($value, $delta);

            if ($leftValue >= $value) {
                $leftValue += $delta;
                $rightValue += $delta;
            }

            $condition = ['and', ['>=', $this->leftAttribute, $leftValue], ['<=', $this->rightAttribute, $rightValue]];
            $this->applyTreeAttributeCondition($condition);

            $this->owner->updateAll(
                [$this->depthAttribute => new Expression($depthAttribute . sprintf('%+d', $depth))],
                $condition
            );

            foreach ([$this->leftAttribute, $this->rightAttribute] as $attribute) {
                $condition = ['and', ['>=', $attribute, $leftValue], ['<=', $attribute, $rightValue]];
                $this->applyTreeAttributeCondition($condition);

                $this->owner->updateAll(
                    [$attribute => new Expression($db->quoteColumnName($attribute) . sprintf('%+d', $value - $leftValue))],
                    $condition
                );
            }

            $this->shiftLeftRightAttribute($rightValue + 1, -$delta);
        } else {
            $leftAttribute = $db->quoteColumnName($this->leftAttribute);
            $rightAttribute = $db->quoteColumnName($this->rightAttribute);
            $nodeRootValue = $this->node->getAttribute($this->treeAttribute);
            $nodeOrderValue = $this->node->getAttribute($this->orderAttribute);
            foreach ([$this->leftAttribute, $this->rightAttribute] as $attribute) {
                $this->owner->updateAll(
                    [$attribute => new Expression($db->quoteColumnName($attribute) . sprintf('%+d', $rightValue - $leftValue + 1))],
                    ['and', ['>=', $attribute, $value], [$this->treeAttribute => $nodeRootValue, $this->orderAttribute => $nodeOrderValue]]
                );
            }

            $delta = $value - $leftValue;

            $this->owner->updateAll(
                [
                    $this->leftAttribute => new Expression($leftAttribute . sprintf('%+d', $delta)),
                    $this->rightAttribute => new Expression($rightAttribute . sprintf('%+d', $delta)),
                    $this->depthAttribute => new Expression($depthAttribute . sprintf('%+d', $depth)),
                    $this->treeAttribute => $nodeRootValue,
                    $this->orderAttribute => $nodeOrderValue,
                ],
                [
                    'and',
                    ['>=', $this->leftAttribute, $leftValue],
                    ['<=', $this->rightAttribute, $rightValue],
                    [$this->treeAttribute => $this->owner->getAttribute($this->treeAttribute)],
                ]
            );

            $this->shiftLeftRightAttribute($rightValue + 1, $leftValue - $rightValue - 1);
        }
    }
}
