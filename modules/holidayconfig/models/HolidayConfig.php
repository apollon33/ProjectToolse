<?php

namespace modules\holidayconfig\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%holiday_config}}".
 *
 * @property integer $id
 * @property integer $month
 * @property integer $day
 * @property string $name
 */
class HolidayConfig extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%holiday_config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['month', 'day'], 'integer'],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'month' => Yii::t('app', 'Month'),
            'day' => Yii::t('app', 'Day'),
            'name' => Yii::t('app', 'Name'),
        ];
    }


}
