<?php

namespace modules\department\models;

use common\behaviors\SortingBehavior;
use modules\position\models\Position;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%department}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 *
 */
class Department extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%department}}';
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
            [['description'], 'string'],
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
            'description' => Yii::t('app', 'Description'),
        ];
    }

    /**
     * @return array
     */
    public static function getList()
    {
        return self::find()->select(['name', 'id'])->orderBy(['sorting' => SORT_ASC])->indexBy('id')->column();
    }

	/**
	 * @return array
	 */
	public static function getDepartments()
	{
		return self::find()->select(['description', 'id'])->indexBy('id')->column();
	}

	/**
	 * @return $array
	 */
	public function getPositionsTree()
	{
		return $this->hasMany(Position::className(), ['department_id' => 'id'])->orderBy(['sorting' => SORT_ASC]);
	}

    public function fields()
    {
        $fields = parent::fields();

        return array_merge($fields, [
            'positions' => 'positionsTree'
        ]);
    }
}
