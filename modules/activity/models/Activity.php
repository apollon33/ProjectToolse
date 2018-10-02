<?php

namespace modules\activity\models;

use common\behaviors\ImageBehavior;
use common\behaviors\TimestampBehavior;
use modules\project\models\Project;
use common\models\User;
use Yii;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%activity}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $project_id
 * @property string $screenshot
 * @property string $target_window
 * @property integer $keyboard_activity_percent
 * @property integer $mouse_activity_percent
 * @property integer $interval
 * @property string $description
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Project $project
 * @property User $user
 */
class Activity extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%activity}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            [
                'class' => ImageBehavior::className(),
                'fieldName' => 'screenshot',
                'inputFileName' => 'screenshot',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'interval'], 'required'],
            [['user_id', 'project_id', 'interval', 'created_at', 'updated_at'], 'integer'],
            [['keyboard_activity_percent', 'mouse_activity_percent'], 'integer', 'min' => 0, 'max' => 100],
            [['target_window', 'description'], 'string', 'max' => 100],
            [['screenshot'], 'string', 'max' => 255],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['project_id' => 'id']],
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
            'project_id' => Yii::t('app', 'Project'),
            'screenshot' => Yii::t('app', 'Screenshot'),
            'target_window' => Yii::t('app', 'Target Window'),
            'keyboard_activity_percent' => Yii::t('app', 'Keyboard Activity Percent'),
            'mouse_activity_percent' => Yii::t('app', 'Mouse Activity Percent'),
            'interval' => Yii::t('app', 'Interval'),
            'description' => Yii::t('app', 'Description'),
            'created' => Yii::t('app', 'Created'),
            'updated' => Yii::t('app', 'Updated'),
            'created_at' => Yii::t('app', 'Created'),
            'updated_at' => Yii::t('app', 'Updated'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
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
     * @return array|null|Activity
     */
    public static function findOneForUser($id)
    {
        $query = self::find()
            ->where(['id' => $id]);
        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role->id == User::TYPE_DEVELOPER) {
            $query->andWhere(['user_id' => Yii::$app->user->id]);
        }
        return $query->one();
    }
}
