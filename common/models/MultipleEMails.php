<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%multiple_e_mails}}".
 *
 * @property integer $id
 * @property integer $userId
 * @property string $email
 * @property integer $type
 * @property integer $active
 *
 * @property User $user
 */
class MultipleEMails extends ActiveRecord
{

    const ACTIVE_NO = 0;
    const ACTIVE_YES = 1;

    const TYPE_PRIVATE = 1;
    const TYPE_PUBLIC = 2;
    const TYPE_GMAIL = 3;

    /**
     * @return array
     */
    public static function getActiveStatuses()
    {
        return [
            self::ACTIVE_NO => Yii::t('yii', 'No'),
            self::ACTIVE_YES => Yii::t('yii', 'Yes'),
        ];
    }

    /**
     * @return array
     */
    public static function getTypeList()
    {
        return [
            self::TYPE_PRIVATE => Yii::t('yii', 'private'),
            self::TYPE_PUBLIC => Yii::t('yii', 'public'),
            self::TYPE_GMAIL => Yii::t('yii', 'gmail'),
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%multiple_e_mails}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'type'], 'required'],
            [['userId', 'type'], 'integer'],
            ['active', 'boolean'],
            [['email'], 'email'],
            [['email'], 'string', 'max' => 100],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'userId' => Yii::t('app', 'User ID'),
            'email' => Yii::t('app', 'Email'),
            'type' => Yii::t('app', 'Type'),
            'active' => Yii::t('app', 'Active'),
        ];
    }

    public function getTypeEmail()
    {
        $multipleEMails = MultipleEMails::getTypeList();
        return $multipleEMails[$this->type];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }

    /**
     * @param $userId
     *
     * @return \yii\db\ActiveQuery
     */
    public static function queryWithMailUser($userId)
    {
        return self::find()->where(['userId' => $userId]);
    }

}
