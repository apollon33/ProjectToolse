<?php

namespace modules\position\models;

use common\behaviors\SortingBehavior;
use common\models\User;
use Yii;
use yii\db\ActiveRecord;
use modules\department\models\Department;

/**
 * This is the model class for table "{{%position}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $department_id
 *
 * @property LogPosition[] $logPositions
 * @property User[] $users
 */
class Position extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%position}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['sorting'], 'integer'],
            [['name'], 'string', 'max' => 100],
	        [[ 'department_id'], 'integer'],
        ];
    }

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			SortingBehavior::className(),
		];
	}

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'department_id' => Yii::t('app', 'Department'),
            'sorting' => Yii::t('app', 'Sorting')
        ];
    }

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getDepartment()
	{
		return $this->hasOne(Department::className(), ['id' => 'department_id']);
	}

	/**
	 * @return $array
	 */
	public function getUsersTree()
	{
		return $this->hasMany(User::className(), ['position_id' => 'id'])->where(['deleted' => User::DELETED_NO, 'active' => User::ACTIVE_YES])->orderBy(['last_name' => SORT_ASC]);
	}

    /**
     * @return array
     */
    public static function getList()
    {
        return self::find()->select(['name', 'id'])->indexBy('id')->column();
    }

    /**
     * @return array|Position[]|ActiveRecord[]
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['position_id' => 'id'])->orderBy(['last_name' => SORT_ASC])->asArray()->all();
    }

    /**
     * @return array
     */
    public function fields()
    {
        $fields = parent::fields();

        return array_merge($fields, [
            'users' => 'users'
        ]);
    }
}
