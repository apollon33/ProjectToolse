<?php

namespace modules\country\models;

use common\models\User;
use modules\client\models\Client;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%country}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $phone_code
 *
 * @property User[] $users
 */
class Country extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%country}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'phone_code'], 'required'],
            [['phone_code'], 'integer'],
            [['name'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Country'),
            'phone_code' => Yii::t('app', 'Phone Code'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['country_id' => 'id']);
    }

    /**
     * @return array
     */
    public static function getList()
    {
        return self::find()->select(['name', 'id'])->indexBy('id')->column();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'name']);
    }
}
