<?php

namespace modules\client\models;

use modules\client\models\Counterparty;
use Yii;
use yii\db\ActiveRecord;
use modules\country\models\Country;
use modules\project\models\Project;

/**
 * This is the model class for table "{{%client}}".
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $skype
 * @property string $phone
 * @property integer $country_id
 * @property string $address
 * @property string $description
 */
class Client extends ActiveRecord
{


    const DELETED_NO = 0;
    const DELETED_YES = 1;


    public static function getDeletedStatuses()
    {
        return [
            self::DELETED_NO => Yii::t('yii', 'No'),
            self::DELETED_YES => Yii::t('yii', 'Yes'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%client}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_name', 'phone', 'email'], 'required'],
            ['email', 'email'],
            [['country_id'], 'integer'],
            [['deleted'], 'boolean'],
            [['company_name', 'timezone'], 'string', 'max' => 100],
            [['email', 'skype', 'phone'], 'string', 'max' => 255],
            [['description'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'client_name' => Yii::t('app', "Customer's name"),
            'company_name' => Yii::t('app', 'Company name'),
            'timezone' => Yii::t('app', 'Timezone'),
            'email' => Yii::t('app', 'Email'),
            'skype' => Yii::t('app', 'Skype'),
            'phone' => Yii::t('app', 'Phone'),
            'country_id' => Yii::t('app', 'Country'),
            'countryName' => Yii::t('app', 'Country'),
            'description' => Yii::t('app', 'Comments'),
        ];
    }

    /**
     * @return array
     */
    public function fieldAttributeLabels() : array
    {
        $listField = $this->attributeLabels();
        unset($listField['country_id']);
        return $listField;
    }

    public static function getList()
    {
        return self::find()->select(['client_name', 'id'])->where(['deleted' => 0])->indexBy('id')->column();
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasMany(Project::className(), ['client_id' => 'id']);
    }

    /**
     * @param bool $deleted
     * @return bool
     */
    private function setDelete($deleted = false)
    {
        $this->deleted = $deleted;
        return $this::save(false);
    }

    /**
     * @return boolean
     */
    public function archive()
    {
        return $this->setDelete(true);
    }

    /**
     * @return boolean
     */
    public function restore()
    {
        return $this->setDelete(false);
    }

    /**

     * @return \yii\db\ActiveQuery
     */
    public function getCounterparties()
    {
        return $this->hasMany(Counterparty::className(), ['client_id' => 'id']);
    }

    /**
     * @param integer $id
     * @return string
     */
    public static function getName($id)
    {
        $client =  Client::find()->select('client_name')->where(['id' => $id])->one();
        return $client->client_name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }

    /**
     * @return string
     */
    public function getCountryName()
    {
        return $this->country->name;
    }

}
