<?php

namespace modules\project\models;

use modules\user_project\models\UserProject;
use Yii;
use yii\db\ActiveRecord;
use modules\profile\models\Profile;
use modules\client\models\Client;

/**
 * This is the model class for table "{{%project}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $client_id
 * @property integer $profile_id
 * @property double $salary
 * @property string $description
 *
 * @property Profile $profile
 * @property Client $client
 */
class Project extends ActiveRecord
{



    const ACCESS_NO = 0;
    const ACCESS_YES = 1;




    /**
     * @inheritdoc
     */

    public $users;
    public static function tableName()
    {
        return '{{%project}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'client_id'], 'required'],
            [['client_id', 'profile_id'], 'integer'],
            [['access'], 'boolean'],
            [['rate'], 'number'],
            [['color'], 'string', 'max' => 7],
            [['description'], 'string'],
            //[['name'], 'string', 'max' => 100],
            [['start_at','end_at'], 'date', 'format' => 'Y-m-d'],
            [['profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => Profile::className(), 'targetAttribute' => ['profile_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
        ];
    }

    /**
     * @return boolean
     */
    public function afterValidate()
    {

        if (!empty($this->start_at)) {
            if(is_int($this->start_at)) {
                $this->start_at=intval($this->start_at);
            } else {
                $this->start_at = strtotime($this->start_at);
            }
        }
        if (!empty($this->end_at)) {
            if(is_int($this->end_at)) {
                $this->end_at=intval($this->end_at);
            } else {
                $this->end_at = strtotime($this->end_at);
            }
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
            'name' => Yii::t('app', 'Name'),
            'client_id' => Yii::t('app', 'Client'),
            'profile_id' => Yii::t('app', 'Profile'),
            'color' => Yii::t('app', 'Color'),
            'rate' => Yii::t('app', 'Rate'),
            'start_at' => Yii::t('app', 'Start At'),
            'end_at' => Yii::t('app', 'End At'),
            'access' => Yii::t('app', 'Access'),
            'project' => Yii::t('app', 'Project'),
            'description' => Yii::t('app', 'Description'),
        ];
    }


    public static function getActiveStatuses()
    {
        return [
            self::ACCESS_NO => Yii::t('yii', 'No'),
            self::ACCESS_YES => Yii::t('yii', 'Yes'),
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['id' => 'profile_id']);

    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /**
     * @param $id
     * @return array
     */
    public function viewUserProject($id)
    {
        $user = UserProject::find()->select('user_id')->where(['project_id' => $id])->all();
        $array = [];
        foreach ($user as $item) {
            $array [] = $item->user_id;
        }
        return $array;
    }

    /**
     * @param $model
     * @param $id
     */
    public function saveUserProject($model, $id)
    {
        if(!empty($model)) {
            foreach ($model as $item) {
                if(!UserProject::find()
                    ->where([
                        'user_id' => $item,
                        'project_id' => $id
                    ])
                    ->one()) {
                    $userProject=new UserProject();
                    $userProject->saveUserProject($id, $item);
                }
            }
            $userProject = UserProject::find()
                ->where(['project_id' => $id])
                ->andWhere(['not in', 'user_id', $model])
                ->all();
            foreach ($userProject as $item) {
                UserProject::findOne($item->id)->delete();
            }
        }else {
            UserProject::deleteAll('project_id ='.$id);
        }
    }


    /**
     * @param $project
     * @param $user
     */
    public function assignmentUser($project,$user)
    {
        if(!empty($project)) {
            foreach ($project as $item) {
                if(!UserProject::find()
                    ->where([
                        'project_id' => $item,
                        'user_id' => $user
                    ])
                    ->one()) {
                    $userProject = new UserProject();
                    $userProject->saveUserProject($item, $user);
                }
            }
            $userProject = UserProject::find()
                ->where(['user_id' => $user])
                ->andWhere(['not in', 'project_id', $project])
                ->all();
            foreach ($userProject as $item) {
                UserProject::findOne($item->id)->delete();
            }
        } else {
            UserProject::deleteAll('user_id ='.$user);
        }
    }


    public function viewProjectAssignment($id)
    {
        $query = Project::find()
            ->select('`project`.`id`,name')
            ->innerJoin('`user_project`', '`project`.`id`  = `user_project`.`project_id`')
            ->where(['user_id' => $id])
            ->orWhere(['access' => 1]);

        $array = [];
        foreach ($query->all() as $item) {
            $array [] = $item->id;
        }
        return $array;
    }


    /**
     * @return array
     */
    public static function getList()
    {
        return self::find()->select(['name', 'id'])->indexBy('id')->column();
    }

    /**
     * @return string
     */
    public function getProject()
    {
        return $this->name;
    }
}
