<?php

namespace modules\holiday\models;

use Yii;
use yii\db\ActiveRecord;
use modules\holidayconfig\models\HolidayConfig;

/**
 * This is the model class for table "{{%holiday}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $date
 */
class Holiday extends ActiveRecord
{
    /**
     * @inheritdoc
     */

    const HOLIDAY=0;
    const SHORT_WORK_DAY=1;
    const COMPANY_DAY=2;

    public static function getTypeHoliday()
    {
        return [
            self::HOLIDAY => Yii::t('yii', 'Holiday'),
            self::SHORT_WORK_DAY => Yii::t('yii', 'Short work day'),
            self::COMPANY_DAY => Yii::t('yii', 'Company day'),
        ];
    }

    public static function tableName()
    {
        return '{{%holiday}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','date','type'], 'required'],

            [['name'], 'string', 'max' => 255],

            [['type'], 'integer'],

            [['date'], 'date', 'format' => 'php:Y-m-d'],

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
            'date' => Yii::t('app', 'Date'),
            'type' => Yii::t('app', 'Type'),
        ];
    }

    public function getType()
    {
        return Holiday::getTypeHoliday()[$this->type];
    }



    public function afterValidate()
    {

        if (!empty($this->date)) {
            $this->date = strtotime($this->date);
        }
        return parent::afterValidate();
    }

    public function copyConfiguration()
    {
        $ten=10;
        $flag=false;
        $holidayConfig = HolidayConfig::find()->all();
        foreach ($holidayConfig as $item) {
            if ($item->day < $ten) {
                $date = '0'.$item->day;
            }
            else {
                $date = $item->day;
            }
            if ($item->month < $ten) {
                $mouth = '0'.$item->month;
            } else {
                $mouth = $item->month;
            }
            $date = date('Y').'-'.$mouth.'-'.$date;
            if($holidayConfig) {
                if(!Holiday::find()->where(['date' => strtotime($date)])->one()) {
                    $flag = true;
                    $this->saveHoliday($date, $item->name);
                }
            } else {
                $flag = true;
                $this->saveHoliday($date, $item->name);
            }
        }
        if($flag) {
            return true;
        } else {
            return false;
        }

    }

    public function saveHoliday($date, $name)
    {
        $this->date = $date;
        $this->name = $name;
        $this->save();
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function listHoliday()
    {
        return self::find()->all();
    }


}
