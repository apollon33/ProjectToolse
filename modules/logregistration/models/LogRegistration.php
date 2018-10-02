<?php

namespace modules\logregistration\models;

use modules\client\models\Counterparty;
use Yii;
use yii\db\ActiveRecord;
use modules\registration\models\Registration;
use common\models\User;
use yii\helpers\ArrayHelper;


/**
 * This is the model class for table "{{%log_registration}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $registration_id
 * @property string $description
 * @property integer $created_at
 *
 * @property Company $company
 * @property Registration $registration
 * @property User $user
 */
class LogRegistration extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%log_registration}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['registration_id'], 'required'],
            [['user_id', 'registration_id'], 'integer'],
            [['description'], 'string'],
            [['created_at'], 'date', 'format' => 'php:Y-m-d'],
            [['registration_id'], 'exist', 'skipOnError' => true, 'targetClass' => Registration::className(), 'targetAttribute' => ['registration_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function afterValidate()
    {
        if (!empty($this->created_at)) {
            $this->created_at = strtotime($this->created_at);
        }
        return parent::afterValidate();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'registration_id' => Yii::t('app', 'Registration'),
            'description' => Yii::t('app', 'Description'),
            'created_at' => Yii::t('app', 'Date'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegistration()
    {
        return $this->hasOne(Registration::className(), ['id' => 'registration_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }


    public static function findModel($id)
    {
        return LogRegistration::find()->orderBy(['created_at' => SORT_DESC])->where(['user_id' => $id])->one();
    }

    public function saveLogRegistration($log, $id)
    {
        $this->user_id = $id;
        $this->created_at = (!empty($log[8])? $log[8]: false);
        $this->registration_id = (!empty($log[10])? $log[10]: false);
        $this->description = (!empty($log[11])? $log[11]: "");
        $this->save();
    }

}
