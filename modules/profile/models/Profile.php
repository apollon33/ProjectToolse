<?php

namespace modules\profile\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%profile}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $first_name
 * @property string $last_name
 * @property double $rate
 * @property string $login
 * @property string $password
 * @property string $email_profile
 * @property string $email_password
 * @property string $skype_profile
 * @property string $skype_password
 * @property string $description
 * @property string $verification
 * @property string $note
 */
class Profile extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%profile}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'first_name', 'last_name'], 'required'],
            [['rate'], 'number'],
            [['note'], 'string'],
            [['name', 'password', 'email', 'email_password', 'skype', 'skype_password', 'description', 'verification'], 'string', 'max' => 255],
            [['first_name', 'last_name', 'login'], 'string', 'max' => 100],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name Profile'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'rate' => Yii::t('app', 'Rate'),
            'login' => Yii::t('app', 'Login'),
            'password' => Yii::t('app', 'Password'),
            'email_profile' => Yii::t('app', 'Email'),
            'email_password' => Yii::t('app', 'Email Password'),
            'skype_profile' => Yii::t('app', 'Skype'),
            'skype_password' => Yii::t('app', 'Skype Password'),
            'description' => Yii::t('app', 'Description'),
            'verification' => Yii::t('app', 'Verification'),
            'note' => Yii::t('app', 'Note'),
            'fullName' => Yii::t('app', 'Full Name'),
        ];
    }

    public function getFullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public static function getList()
    {
        return ArrayHelper::map(Profile::find()->orderBy(['last_name' => SORT_ASC])->all(), 'id', function ($client, $defaultValue){
            return $client->first_name . ' ' . $client->last_name;
        });
    }
}
