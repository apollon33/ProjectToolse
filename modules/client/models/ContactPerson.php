<?php
namespace modules\client\models;

use yii\db\ActiveRecord;
use Yii;

/**
 * Class ContactPerson
 * @package modules\company\models
 */
class ContactPerson extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%contact_person}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Contact Person ID'),
            'company_id' => Yii::t('app', 'Counterparty ID'),
            'payer_address' => Yii::t('app', "Payer's address"),
            'phone' => Yii::t('app', 'Contact phone'),
            'email' => Yii::t('app', 'Contact email'),
        ];
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['company_id'], 'integer'],
            [['payer_address', 'phone', 'email'], 'required'],
            ['email', 'email'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCounterparty()
    {
        return $this->hasOne(Counterparty::className(), ['id' => 'company_id']);
    }
}
