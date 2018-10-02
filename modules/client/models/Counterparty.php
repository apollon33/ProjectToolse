<?php

namespace modules\client\models;


use modules\country\models\Country;
use yii\db\ActiveRecord;
use Yii;
use modules\client\models\ContactPerson;

/**
 * Class Counterparty
 * @package modules\client\models
 */
class Counterparty extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%counterparty}}';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'address', 'country'], 'required'],
            [['name', 'type', 'registration_number', 'vat', 'timezone', 'city', 'address', 'currency', 'bank_name', 'iban', 'swift'], 'string', 'max' => 100],
            [['comments', 'payment_method'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Counterparty name'),
            'type' => Yii::t('app', 'Counterparty type'),
            'registration_number' => Yii::t('app', 'Registration number'),
            'vat' => Yii::t('app', 'VAT number'),
            'timezone' => Yii::t('app', 'Timezone'),
            'country' => Yii::t('app', 'Country'),
            'city' => Yii::t('app', 'City'),
            'address' => Yii::t('app', 'Address'),
            'payment_method' => Yii::t('app', 'Payment method'),
            'currency' => Yii::t('app', 'Currency'),
            'bank_name' => Yii::t('app', 'Bank name'),
            'comments'=> Yii::t('app', 'Comments'),
            'iban' => Yii::t('app', 'IBAN'),
            'swift' => Yii::t('app', 'Swift'),
        ];
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
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContactPerson()
    {
        return $this->hasMany(ContactPerson::className(), ['company_id' => 'id']);
    }

    /**
     * @return string
     */
    public function getCountryName()
    {
        return Country::getList()[$this->country] ?? $this->country;
    }
}
