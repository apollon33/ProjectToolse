<?php

namespace common\models;

use yii\rbac\Role;
use yii\db\ActiveRecord;

class RoleModel extends ActiveRecord
{
    /**
     * @var int the type of the item. This should be either [[TYPE_ROLE]] or [[TYPE_PERMISSION]].
     */
    public $type = Role::TYPE_ROLE;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_item}}';
    }
}