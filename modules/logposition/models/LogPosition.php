<?php

namespace modules\logposition\models;


use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use modules\position\models\Position;
use common\models\User;

/**
 * This is the model class for table "{{%log_position}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $position_id
 * @property string $description
 * @property integer $created_at
 *
 * @property Position $position
 * @property User $user
 */
class LogPosition extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%log_position}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['position_id','user_id'], 'required'],
            [['user_id', 'position_id'], 'integer'],
            [['description'], 'string'],
            [['created_at'], 'date', 'format' => 'php:Y-m-d'],
            [['position_id'], 'exist', 'skipOnError' => true, 'targetClass' => Position::className(), 'targetAttribute' => ['position_id' => 'id']],
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
            'position_id' => Yii::t('app', 'Position'),
            'description' => Yii::t('app', 'Description'),
            'created_at' => Yii::t('app', 'Date'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPosition()
    {
        return $this->hasOne(Position::className(), ['id' => 'position_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @param $id
     * @return array|null|ActiveRecord
     */
    public static function findModel($id)
    {
        return LogPosition::find()->orderBy(['created_at' => SORT_DESC])->where(['user_id' => $id])->one();
    }

    /**
     * @param $log
     * @param $id
     */
    public function saveLogPosition($log, $id)
    {
        $this->user_id = $id;
        $this->created_at = (!empty($log[2])? $log[2]: false);
        $this->position_id = (!empty($log[4])? $log[4]: false);
        $this->description = (!empty($log[5])? $log[5]: "");
        $this->save();

    }
}
