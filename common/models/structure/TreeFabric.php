<?php
namespace common\models\structure;

use kartik\tree\models\Tree;

/**
 * Class TreeFabric
 * @package modules\user\models\structure
 */
class TreeFabric extends Tree
{
	/**
	 * @ default vars
	 */
	public $id;
	public $root;
	public $lvl;
	public $name;
	public $description   = '';
	public $child = false;
	public $icon          = null;
	public $icon_type     = 1;
	public $active        = 1;
	public $selected      = 0;
	public $disabled      = 0;
	public $readonly      = 0;
	public $visible       = 1;
	public $collapsed     = 1;
	public $movable_u     = 1;
	public $movable_d     = 1;
	public $movable_l     = 1;
	public $movable_r     = 1;
	public $removable     = 1;
	public $removable_all = 0;
    public $isNewRecord   = 0;

	public $sourceClassName;
	const USER_CLASS_NAME       = 'common\models\User';
	const POSITION_CLASS_NAME   = 'modules\position\models\Position';
	const DEPARTMENT_CLASS_NAME = 'modules\department\models\Department';

    public static $keys = [
        self::USER_CLASS_NAME => 'user',
        self::POSITION_CLASS_NAME => 'position',
        self::DEPARTMENT_CLASS_NAME => 'department',
    ];

    /**
     * @param array $sourceObject
     * @param null $root
     */
	public function __construct($sourceObject = null, $root = null)
	{
        parent::__construct();

        if (!empty($sourceObject) && !empty($root)) {
            $this->sourceClassName = $sourceObject::className();
            $this->id = self::$keys[$this->sourceClassName] . '-' . $sourceObject->id;
            $this->root = $root;

            switch ($this->sourceClassName) {
                case $this::USER_CLASS_NAME:
                    $this->lvl = 2;
                    $this->name = $sourceObject->last_name . ' ' . $sourceObject->first_name;
                    $this->child = true;
                    break;
                case $this::POSITION_CLASS_NAME:
                    $this->lvl = 1;
                    $this->name = $sourceObject->name;
                    break;
                case $this::DEPARTMENT_CLASS_NAME:
                    $this->lvl = 0;
                    $this->name = $sourceObject->name;
                    break;
            }
        }
	}

    /**
     * @param $key
     * @return className
     */
    public static function getClassNameByKey($key)
    {
        if (!empty($key)) {
            return array_flip(self::$keys)[$key];
        } else {
            return self::className();
        }
    }
}
