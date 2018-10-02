<?php

namespace modules\actioncalendar\models;

use Yii;
use yii\db\ActiveRecord;
use modules\holiday\models\Holiday;
use common\models\User;

/**
 * This is the model class for table "{{%event}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $type
 * @property integer $start_at
 * @property integer $end_at
 * @property integer $active
 */
class Event extends ActiveRecord
{


    const ACTIVE_NO = 0;
    const ACTIVE_YES = 1;

    public static function getActiveStatuses()
    {
        return [
            self::ACTIVE_NO => Yii::t('yii', 'No'),
            self::ACTIVE_YES => Yii::t('yii', 'Yes'),
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%event}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['type', 'active','created_by',], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['description', 'location'], 'string'],
            [['start_at'],'date','timestampAttribute' => 'start_at',  'format' => 'yyyy-MM-dd HH:mm'],
            [['end_at'],'date','timestampAttribute' => 'end_at',  'format' => 'yyyy-MM-dd HH:mm'],
            [['end_at'], 'compare', 'compareAttribute' => 'start_at', 'operator' => '>'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name of event'),
            'type' => Yii::t('app', 'Category'),
            'location' => Yii::t('app', 'Location'),
            'description' => Yii::t('app', 'Description'),
            'created_by' => Yii::t('app', 'Publicist'),
            'start_at' => Yii::t('app', 'Pick start time'),
            'end_at' => Yii::t('app', 'Pick end time'),
            'active' => Yii::t('app', 'Active'),
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if(empty($this->created_by)) {
            $this->created_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }

    public function getType()
    {
        return Holiday::getTypeHoliday()[$this->type];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    public function getInterval()
    {
        return (!empty($this->start_at) ? Yii::$app->formatter->asDatetime($this->start_at) : 'Not Set' ).' - '. (!empty($this->end_at) ?  Yii::$app->formatter->asDatetime($this->end_at) : 'Not Set' );
    }

}
