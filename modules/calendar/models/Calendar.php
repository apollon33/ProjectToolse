<?php

namespace modules\calendar\models;

use Yii;
use yii\db\ActiveRecord;
use common\behaviors\TimestampBehavior;
use yii\helpers\Json;
use common\components\DurationConverter;
use modules\project\models\Project;
use common\models\User;
use modules\vacation\models\Vacation;
use modules\holiday\models\Holiday;
use modules\countingtime\models\CountingTime;

/**
 * This is the model class for table "{{%calendar}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $estimated_time
 * @property integer $estimate_approval
 * @property integer $actual_time
 * @property integer $start_at
 * @property integer $end_at
 * @property string $description
 *
 * @property Project $project
 * @property User $user
 */
class Calendar extends ActiveRecord
{

    const APPROVAL_NO = 0;
    const APPROVAL_YES = 1;

    const SCENARIO_SAVE_CALENDAR = 'save_calendar';
    const SCENARIO_SAVE = 'save';

    public static function getApprovals()
    {
        return [
            self::APPROVAL_NO => Yii::t('yii', 'No'),
            self::APPROVAL_YES => Yii::t('yii', 'Yes'),
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public $hour;
    public $year;
    public $month;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%calendar}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_id'], 'required'],
            [['user_id', 'project_id','created_by','actual_time'], 'integer'],
            ['project_id', 'required', 'message' => 'This user is not assigned to any project'],
            [['description', 'estimated_time'], 'string'],
            [['start_at'],'date','timestampAttribute' => 'start_at',  'format' => 'yyyy-MM-dd HH:mm'],
            [['end_at'],'date','timestampAttribute' => 'end_at',  'format' => 'yyyy-MM-dd HH:mm'],
            [['end_at'], 'compare', 'compareAttribute' => 'start_at', 'operator' => '>'],
            [['year', 'month'], 'integer'],
            ['estimated_time', 'match', 'pattern' => '/^([0-9]{1,4}w)?( )*([0-9]{1,4}d)?( )*([0-9]{1,2}h)?( )*([0-9]{1,2}m)?$/'],
            [['estimate_approval'], 'boolean'],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['project_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'user_id' => Yii::t('app', 'User'),
            'project_id' => Yii::t('app', 'Project'),
            'estimated_time' => Yii::t('app', 'Estimated time'),
            'estimate_approval' => Yii::t('app', 'Estimate approval'),
            'actual_time' => Yii::t('app', 'Spent time'),
            'start_at' => Yii::t('app', 'Start At'),
            'end_at' => Yii::t('app', 'End At'),
            'created_by' => Yii::t('app', 'Manager'),
            'created_at' => Yii::t('app', 'Created'),
            'updated_at' => Yii::t('app', 'Updated'),
            'created' => Yii::t('app', 'Created'),
            'updated' => Yii::t('app', 'Updated'),
            'description' => Yii::t('app', 'Description'),
            'year' => Yii::t('app', 'Year'),
            'month' => Yii::t('app', 'Month'),
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_SAVE_CALENDAR => [
                'id',
                'project_id',
                'start_at',
                'actual_time',
                'end_at',
                'created_by',
                'description',
                'year',
                'month',
            ],
            self::SCENARIO_SAVE => [
                'id',
                'user_id',
                'project_id',
                'start_at',
                'actual_time',
                'end_at',
                'created_by',
                'description',
                'year',
                'month',
            ],
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
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    public function getTaskTime()
    {
        $countingtime = new CountingTime();

        $workday = 8;

        return DurationConverter::shortDuration(Yii::$app->formatter->asDuration($countingtime->jobTime($this->start_at, $this->end_at, $workday )));

    }

    public function getInterval()
    {
        return (!empty($this->start_at) ? Yii::$app->formatter->asDatetime($this->start_at) : 'Not Set' ).' - '. (!empty($this->end_at) ?  Yii::$app->formatter->asDatetime($this->end_at) : 'Not Set' );
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->estimated_time = DurationConverter::calculateTime($this->estimated_time);
        return parent::beforeSave($insert);
    }

    /**
     * @param $id
     * @return array
     */
    public function allCalendar($id=false, $year=false, $month=false, $created_by=false)
    {
        $events = array();
        $start_at = strtotime((!empty($year)  ?  $year : date('Y')).'-'.(!empty($month)  ?  $month : date('F')).'-01');
        $end_at = strtotime((!empty($year)  ?  $year : date('Y')).'-'.(!empty($month)  ?  $month : date('F')).'-01 +1 month' );
        $created_by = (!empty($created_by)  ?  intval($created_by) : Yii::$app->user->identity->id );

            if($start_at !== false) {
                if( Yii::$app->user->identity->isAllowedToViewStage() && Yii::$app->user->identity->id === $created_by) {
                    $model =  Calendar::find()
                        ->where(['between', 'start_at', $start_at, $end_at])
                        ->andWhere(['user_id' => $id])
                        ->all();
                } else {
                    $model = Calendar::find()
                        ->where(['between', 'start_at', $start_at, $end_at])
                        ->andWhere([
                            'user_id' => $id,
                            'created_by' => $created_by
                        ])
                        ->all();
                }
            } else {
                $model = Calendar::find()
                    ->where(['user_id' => $id])
                    ->all();
            }

        foreach ($model as $value) {
            $events[] = $this->fillEventTable($value);
        }
        return  $events;
    }


    public function countDayMonth($month=false, $year=false)
    {
        $lastDayMonth = cal_days_in_month(CAL_GREGORIAN, (!empty($month) ? $month : date('n')), (!empty($year) ? $year : date('Y')));
        $days = range(0, $lastDayMonth);
        array_push($days, Yii::t('app', 'time'), Yii::t('app', 'week time'));
        return $days;
    }


    /**
     * @param bool $id
     * @param bool $start_at
     * @param bool $end_at
     * @param bool $created_by
     * @return array
     */
    public function reportCart($id=false, $year=false, $month=false, $created_by=false)
    {
        $events=array();
        $start_at = strtotime((!empty($year)  ?  $year : date('Y') ).'-'.(!empty($month)  ?  $month : date('F') ).'-01' );
        $end_at = strtotime((!empty($year)  ?  $year : date('Y')).'-'.(!empty($month)  ?  $month : date('F')).'-01 +1 month' );
        $created_by = (!empty($created_by)  ?  intval($created_by) : Yii::$app->user->identity->id);

        $model = Calendar::find()
            ->where(['between', 'start_at', $start_at, $end_at])
            ->andWhere([
                'user_id' => $id,
                'created_by' => $created_by
            ])
            ->all();

        foreach ($model as $value) {
            $events[] = $this->fillEventTable($value);
        }

        return  $events;
    }


    public function filterCalendar($post)
    {
        $events=array();

        $model = Calendar::find()
            ->where(['in', 'user_id', $post['user']])
            ->andWhere(['in', 'project_id', $post['project']])
            ->all();

        foreach ($model as $value) {
            $events[] = $this->fillEvent($value);
        }

        return  $events;
    }

    /**
     *
     * @param array $model
     * @return array
     *
     */
    public function fillEvent ($model)
    {
        $project = Project::findOne(['id' => $model->project_id]);

        $Event = new \yii2fullcalendar\models\Event();
        $Event->id = $model->id;
        $Event->title = Yii::t('app', 'Project').' ( '.$project->name.' )';
        $Event->color = $project->color;
        $Event->className = 'calendar';
        $Event->start = date('Y-m-d\TH:i:s\Z',$model->start_at);
        $Event->end = date('Y-m-d\TH:i:s\Z',$model->end_at);
        return $Event;
    }

    public function fillEventTable ($model)
    {
        $Event = [
            'id' => $model->id,
            'id_user' => $model->user_id,
            'id_project' => $model->project_id,
            'actual_time' => $model->actual_time,
            'start' => date('d', $model->start_at),
            'comment' => ( !empty($model->description) ? $model->description : false ),
        ];
        return  $Event;
    }

    /**
     *
     * @param int $user_id
     * @return array
     *
     */
    public function activeProjectUser($user_id)
    {
        $query = Project::find()
            ->select('`project`.`id`,name')
            ->innerJoin('`user_project`', '`project`.`id`  = `user_project`.`project_id`');

        if (!empty($user_id)) {
            $query->where(['user_id' => $user_id])
                ->orWhere(['access' => 1]);
        }

        return $query->all();
    }

    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function allProjectUser($id)
    {
        return Project::find()
            ->select('`project`.`id`,name')
            ->innerJoin('`user_project`', '`project`.`id`  = `user_project`.`project_id`')
            ->where(['user_id' => $id])->asArray()
            ->all();
    }

    /**
     * @param $calendar
     * @param $project
     * @return mixed
     */
    public function allUserProject($calendar, $project)
    {
        foreach ($calendar as $value) {
            $project[] = Project::find()
                ->select('id,name')
                ->where(['id' => $value['id_project']])
                ->asArray()
                ->one();
        }
        $result = array_reduce($project, function ($a, $b) {
            static $stored = array();

            $hash = md5(serialize($b));

            if (!in_array($hash, $stored)) {
                $stored[] = $hash;
                $a[] = $b;
            }

            return $a;
        }, array());
        return $result;
    }

    /**
     * @param bool $vac
     * @param bool $user_id
     * @param bool $day
     * @param bool $year
     * @param bool $month
     * @return array|bool
     */
    public function vacationUser($vac = false, $user_id = false, $day = false, $year = false, $month = false)
    {
        $vacation = Vacation::find()->where(['user_id' => $user_id])->all();
        $i = 1;
        while($day >= $i){
            foreach ($vacation as $item) {
                if(!empty($item) ) {
                    if($i >= intval(date('j', $item->start_at)) && $i <= intval(date('j', $item->end_at))
                        && intval($year) === intval(date('Y', $item->start_at)) && intval($year) === intval(date('Y', $item->end_at))
                        && $month === date('F', $item->start_at) && $month === date('F', $item->end_at)) {
                        $vac[] = $i;
                    }
                }
            }
            $i++;
        }
        return $vac;
    }

    /**
     * @param $month
     * @return string
     */
    public function holidays($month)
    {
        $holidays = [];
        $holiday = Holiday::find()->select('date')->all();
        foreach ($holiday as $item) {
            if($month === date('n', $item->date)) {
                $holidays[] = intval(date('j', $item->date));
            }
        }
        return $holidays;
    }

    /**
     * @param bool $id
     * @param bool $year
     * @param bool $month
     * @param bool $created_by
     * @return array|\yii\db\ActiveRecord[]
     */
    public function allUserReportCart($id = false, $year = false, $month = false, $createdBy = false)
    {
        return Calendar::find()
            ->select('user_id')
            ->where(['between', 'start_at', strtotime((!empty($year)  ?  $year : date('Y')).'-'.(!empty($month)  ?  $month : date('F')).'-01'), strtotime((!empty($year)  ?  $year : date('Y')).'-'.(!empty($month)  ?  $month : date('F')).'-01 +1 month' )])
            ->andWhere(['created_by' => (!empty($createdBy)  ?  $createdBy : Yii::$app->user->identity->id)])
            ->andWhere((!empty($id)  ?  ['user_id'=>$id] : ''))
            ->groupBy('user_id')
            ->all();
    }
}