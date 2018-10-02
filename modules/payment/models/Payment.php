<?php

namespace modules\payment\models;

use modules\logregistration\models\LogRegistration;
use Yii;
use yii\db\ActiveRecord;
use common\models\User;
use common\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%payment}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $date
 * @property integer $type
 * @property double $amount
 * @property double $tax_profit
 * @property double $tax_war
 * @property double $tax_pension
 * @property double $payout
 *
 * @property User $user
 */
class Payment extends ActiveRecord
{


    const PREPAIN_EXPENSE = 1;
    const SALARY = 2;
    const CIVIL_CONTRACT_PYAMENT = 3;
    const ENTEPRENEUR_PAYMENT = 4;


    public static function getType()
    {
        return [
            self::PREPAIN_EXPENSE => Yii::t('app', 'Prepaid expense'),
            self::SALARY => Yii::t('app', 'Salary'),
            self::CIVIL_CONTRACT_PYAMENT => Yii::t('app', 'Civil contract payment'),
            self::ENTEPRENEUR_PAYMENT => Yii::t('app', 'Entepreneur payment'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%payment}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'amount', 'tax_profit', 'tax_war', 'tax_pension', 'payout', 'mounth', 'year'], 'required'],
            [['user_id', 'type', 'mounth', 'year', 'created_at', 'updated_at'], 'integer'],
            [['amount', 'tax_profit', 'tax_war', 'tax_pension', 'payout'], 'compare', 'compareValue' => 0, 'operator' => '>=', 'type' => 'number'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'mounth' => Yii::t('app', 'Month'),
            'year' => Yii::t('app', 'Year'),
            'type' => Yii::t('app', 'Type'),
            'amount' => Yii::t('app', 'Amount'),
            'tax_profit' => Yii::t('app', 'Tax Profit'),
            'tax_war' => Yii::t('app', 'Tax War'),
            'tax_pension' => Yii::t('app', 'Tax Pension'),
            'payout' => Yii::t('app', 'Payout'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getPayment()
    {
        return Payment::getType()[$this->type];
    }

    public static function listRandom()
    {
        $year = 2017;
        $count = date('Y') - $year + 5;
        for ($i = $year; $i <= ($year + $count); $i++) {
            $arr[$i] = $i;
        }

        return $arr;
    }




}
