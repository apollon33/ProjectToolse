<?php

namespace modules\logsalary\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\User;

/**
 * This is the model class for table "{{%log_salary}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property double $salary
 * @property integer $currency
 * @property double $reporting_salary
 * @property string $description
 * @property integer $created_at
 *
 * @property User $user
 */
class LogSalary extends ActiveRecord
{


    const CURRENCY_UAH = 1;
    const CURRENCY_USD = 2;
    const CURRENCY_EUR = 3;


    public static function getCurrency()
    {
        return [
            self::CURRENCY_UAH => Yii::t('app', '₴ UAH'),
            self::CURRENCY_USD => Yii::t('app', '$ USD'),
            self::CURRENCY_EUR => Yii::t('app', '€ EUR'),
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%log_salary}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['salary', 'currency','reporting_salary','bonus_currency','bonus'], 'required'],
            [['user_id', 'currency','bonus_currency'], 'integer'],
            [['salary', 'reporting_salary','bonus'], 'number'],
            [['description'], 'string'],
            [['created_at'], 'date', 'format' => 'Y-m-d'],
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
            'user_id' => Yii::t('app', 'User ID'),
            'salary' => Yii::t('app', 'Salary'),
            'currency' => Yii::t('app', 'Currency'),
            'bonus_currency' => Yii::t('app', 'Bonus Currency'),
            'reporting_salary' => Yii::t('app', 'Reporting Salary'),
            'bonus' => Yii::t('app', 'Bonus'),
            'description' => Yii::t('app', 'Description'),
            'created_at' => Yii::t('app', 'Date'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function afterValidate()
    {
        if (!empty($this->created_at)) {
            $this->created_at = strtotime($this->created_at);
        }
        return parent::afterValidate();
    }

    public  function getCurrencyName()
    {
        return self::getCurrency()[$this->gender];
    }



    public static function findModel($id)
    {
        return LogSalary::find()->orderBy(['created_at' => SORT_DESC])->where(['user_id' => $id])->one();
    }

    public function saveLogSalary($log, $id)
    {
        $this->user_id = $id;
        $this->created_at = (!empty($log[14])? $log[14]: false);
        $this->salary = (!empty($log[16])? $log[16]: false);
        $this->currency = (!empty($log[17])? $log[17]: false);
        $this->bonus = (!empty($log[18])? $log[18]: false);
        $this->bonus_currency = (!empty($log[19])? $log[19]: false);
        $this->reporting_salary = (!empty($log[20])? $log[20]: false);
        $this->description = (!empty($log[21])? $log[21]: "");
        $this->save();
    }

}
