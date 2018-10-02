<?php

namespace modules\registration\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%registration}}".
 *
 * @property integer $id
 * @property string $name
 *
 * @property LogRegistration[] $logRegistrations
 * @property User[] $users
 */
class Registration extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%registration}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
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
            'name' => Yii::t('app', 'Name'),
        ];
    }

    /**
     * @return array
     */
    public static function getList()
    {
        return self::find()->select(['name', 'id'])->indexBy('id')->column();
    }
}
