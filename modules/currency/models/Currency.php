<?php

namespace modules\currency\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%currency}}".
 *
 * @property integer $id
 * @property string $key_digital
 * @property string $key_letter
 * @property integer $datу
 * @property string $name
 * @property double $rate
 */
class Currency extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%currency}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key_digital', 'key_letter', 'date', 'name', 'rate'], 'required'],
            [['date'], 'integer'],
            [['rate'], 'number' ],
            [['key_digital'], 'integer'],
            [['key_letter'], 'string', 'max' => 10],
            [['name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'key_digital' => Yii::t('app', 'Key Digital'),
            'key_letter' => Yii::t('app', 'Key Letter'),
            'date' => Yii::t('app', 'Date'),
            'name' => Yii::t('app', 'Name'),
            'rate' => Yii::t('app', 'Rate'),
            'Rate' => Yii::t('app', 'Rate'),
        ];
    }


    public function getRate(){

        return $this->key_letter.' '.$this->rate;

    }


    public function apiSaveCarrency($item)
    {
        $this->key_digital = $item['r030'];
        $this->key_letter = $item['cc'];
        $this->date = time();
        $this->name = $item['txt'];
        $this->rate = $item['rate'];
        if ($this->save()) {
            return true;
        }

    }

    /**
     * @return array
     */
    public static function getList()
    {
        return self::find()->select(['name', 'id'])->indexBy('id')->column();
    }
}
