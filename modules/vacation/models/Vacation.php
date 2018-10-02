<?php

namespace modules\vacation\models;


use common\components\Mailer;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

use common\models\User;
use modules\holiday\models\Holiday;

use common\helpers\Toolbar;

use Carbon\Carbon;

/**
 * This is the model class for table "{{%vacation}}".
 *
 * @property integer $id
 * @property integer $id_user
 * @property integer $start_at
 * @property integer $end_at
 *
 * @property User $idUser
 */
class Vacation extends ActiveRecord
{
    const TYPE_TARIFF = 1;
    const TYPE_NOT_PAID = 2;
    const TYPE_SICK_LEAVE = 3;

    const APPROVE_NO = 0;
    const APPROVE_YES = 1;

    const SCENARIO_CART_USER = 'cart_user';
    const SCENARIO_SAVE_USER = 'save_vacation';

    /**
     * number of days of the first leave per year
     */
    const VACATION_DAYS_PER_YEAR_OF_EXPERIENCE = 5;

    public $checkbox = false;
    public $year;
    public $month;

    public static function getApproveStatuses()
    {
        return [
            self::APPROVE_NO => Yii::t('yii', 'No'),
            self::APPROVE_YES => Yii::t('yii', 'Yes'),
        ];
    }

    public static function getCurrency()
    {
        return [
            self::TYPE_TARIFF => Yii::t('app', 'Paid leave'),
            self::TYPE_NOT_PAID => Yii::t('app', 'Not paid'),
            self::TYPE_SICK_LEAVE => Yii::t('app', 'Sick leave'),
        ];
    }

    public static function getCurrencyColor()
    {
        return [
            self::TYPE_TARIFF => '#64bf4e',
            self::TYPE_NOT_PAID => '#d61e11',
            self::TYPE_SICK_LEAVE => '#007dd1',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%vacation}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'managerId', 'approve', 'start_at', 'end_at' ], 'required'],
            [['user_id', 'type', 'approve', 'managerId'], 'integer'],
            [['description'], 'string'],
            ['type', 'validationType'],
            ['checkbox', 'boolean'],
            [['year', 'month'], 'integer'],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => ['user_id' => 'id'],
            ],
            ['start_at', 'date', 'timestampAttribute' => 'start_at', 'format' => 'php:Y-m-d', 'min' => date('Y-m-d'), 'tooSmall' => Yii::t('yii', '{attribute} must be no less than {min}.')],
            ['end_at', 'date', 'timestampAttribute' => 'end_at', 'format' => 'php:Y-m-d'],
            ['end_at', 'compare', 'compareAttribute' => 'start_at', 'operator' => '>='],
            [['start_at','end_at' ], 'validationStartDate']
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_CART_USER => [
                'checkbox',
                'user_id',
                'start_at',
                'end_at',
                'type',
                'description',
                'year',
                'month',
                'managerId',
                'approve',
            ],
            self::SCENARIO_SAVE_USER => [
                'checkbox',
                'user_id',
                'start_at',
                'end_at',
                'type',
                'description',
                'year',
                'month',
                'managerId',
                'approve',
            ],
        ];
    }

    public function beforeValidate()
    {
        if ($this->start_at <= $this->end_at) {
            if (!is_int($this->start_at) ? (ctype_digit($this->start_at)) : true) {
                $this->start_at = intval($this->start_at);
            }
            if (!is_int($this->end_at) ? (ctype_digit($this->end_at)) : true) {
                $this->end_at = intval($this->end_at);
            }
        }

        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'checkbox' => Yii::t('app', 'Allow'),
            'user_id' => Yii::t('app', 'User'),
            'start_at' => Yii::t('app', 'Start At'),
            'end_at' => Yii::t('app', 'End At'),
            'type' => Yii::t('app', 'Type'),
            'description' => Yii::t('app', 'Description'),
            'year' => Yii::t('app', 'Year'),
            'month' => Yii::t('app', 'Month'),
            'managerId' => Yii::t('app', 'Manager'),
            'approve' => Yii::t('app', 'Approve'),
            'currencyList' => Yii::t('app', 'Currency List'),
            'startAt' => Yii::t('app', 'Start At'),
        ];
    }

    /**
     * @return mixed
     */
    public function getCurrencyList()
    {
        return Vacation::getCurrency()[$this->type];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return string
     */
    public function getStartAt()
    {
        return Yii::$app->formatter->asDate($this->start_at) . ' - ' . Yii::$app->formatter->asDate($this->end_at);
    }

    /**
     * @param $userId
     * @param $date Carbon
     * @param array $type
     *
     * @return \yii\db\ActiveQuery
     */
    public static function getAllVacation($userId, $date, $type)
    {
        $endYear = $date->endOfYear()->timestamp;
        $startYear = $date->startOfYear()->timestamp;
        return Vacation::find()
            ->where([
                'user_id' => $userId,
                'type' => $type,
            ])
            ->andWhere(['between', 'start_at', $startYear, $endYear])
            ->andWhere(['between', 'end_at', $startYear, $endYear]);
    }

    /**
     * @param User $user
     *
     * @return int
     */
    public static function experienceUser($user, $startAt = false)
    {
        $startAt = !$startAt ? time() : strtotime($startAt);
        $dateReceipt = (isset($user->date_receipt)) ? $user->date_receipt : time();
        $maxWeekVacation = 3;
        $date = Carbon::now();
        $endYear = $date->endOfYear()->timestamp;
        $startYear = $date->startOfYear()->timestamp;
        $sixMonth = strtotime(date("Y-m-d", $dateReceipt) . " +6 month");
        $year = $endYear - $startYear;
        $receipt = $startAt - $dateReceipt;
        if ($dateReceipt + $receipt < $sixMonth) {
            return 0;
        }
        $sumVacation = intval($receipt / $year);
        if ($sumVacation > $maxWeekVacation) {
            $sumVacation = $maxWeekVacation;
        }

        return empty($sumVacation) ?
            self::VACATION_DAYS_PER_YEAR_OF_EXPERIENCE :
            self::VACATION_DAYS_PER_YEAR_OF_EXPERIENCE * $sumVacation;
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validationType($attribute, $params)
    {
        if (!intval($this->checkbox)) {
            if (intval($this->type) === Vacation::TYPE_TARIFF) {
                $date = Carbon::now();
                $vacations = self::getAllVacation($this->user_id, $date->copy(),[Vacation::TYPE_TARIFF])->all();
                $dateReceipt = empty($this->user->date_receipt) ? time() : $this->user->date_receipt;
                if (($this->countVacationDaysTariff() + $this->countVacationDays($vacations)) > self::experienceUser($this->user, $this->start_at)) {
                    $day = self::experienceUser($this->user) - $this->countVacationDays($vacations);

                    return $this->addError($attribute, Yii::t('app', 'You have {day} vacation days', ['day' => $day]));
                }
            }
        }
    }

    /**
     * @param array $vacations
     *
     * @return false|int|string
     */
    public function countVacationDays($vacations)
    {
        $finish= 0;

        foreach ($vacations as $vacation) {
            if ($vacation->id !== $this->id) {
                $finish += $vacation->countVacationDaysTariff();
            }
        }

        return $finish;
    }


    /**
     * @param $start_at
     * @param $end_at
     *
     * @return false|int|string
     */
    public function countVacationDaysTariff()
    {
        $startAt = !is_int($this->start_at) ? strtotime($this->start_at) : $this->start_at;
        $endAt = !is_int($this->end_at) ? strtotime($this->end_at) : $this->end_at;
        if (date('Y', $endAt) !== date('Y', $startAt)) {
            if(date('Y', $startAt) === $this->year ) {
                $endAt = Carbon::create(intval(date('Y', $startAt)));
                $endAt = ($endAt->endOfYear()->timestamp) - (24 * 60 * 60);
            } else {
                $startAt = Carbon::create(intval(date('Y', $endAt)));
                $startAt = ($startAt->startOfYear()->timestamp);
            }
        }

        $days = ($endAt - $startAt) / 86400 + 1;

        $noFullWeeks = floor($days / 7);
        $noRemainingDays = fmod($days, 7);

        $FirstDayOfWeek = date("N", $startAt);
        $LastDayOfWeek = date("N", $endAt);

        if ($FirstDayOfWeek <= $LastDayOfWeek) {
            if ($FirstDayOfWeek <= 6 && 6 <= $LastDayOfWeek) {
                $noRemainingDays--;
            }
            if ($FirstDayOfWeek <= 7 && 7 <= $LastDayOfWeek) {
                $noRemainingDays--;
            }
        } else {
            if ($FirstDayOfWeek == 7) {
                $noRemainingDays--;

                if ($LastDayOfWeek == 6) {
                    $noRemainingDays--;
                }
            } else {
                $noRemainingDays -= 2;
            }
        }

        $workingDays = $noFullWeeks * 5;
        if ($noRemainingDays > 0) {
            $workingDays += $noRemainingDays;
        }

        $holidays = Holiday::listHoliday();

        foreach ($holidays as $holiday) {
            $timeStamp = $holiday->date;
            if ($startAt <= $timeStamp && $timeStamp <= $endAt && date("N", $timeStamp) != 6 && date("N",
                    $timeStamp) != 7
            ) {
                $workingDays--;
            }
        }

        return $workingDays;
    }

    /**
     * @return array
     */
    public function remainingVacationDays($users, $year)
    {
        $leftVacation = [];
        foreach ($users as $user) {
            $date = Carbon::createFromDate($year);
            $allVacationYear = self::getAllVacation($user->id, $date->copy(), [Vacation::TYPE_TARIFF])->all();
            $leftVacation[] = [
                'day' => (self::experienceUser($user, $this->start_at)) - $this->countVacationDays($allVacationYear),
                'userId' => $user->id,
            ];
        }
        $leftVacation = ArrayHelper::index($leftVacation, 'userId');

        return $leftVacation;
    }

    /**
     * @param User $user
     *
     * @return int
     */
    public static  function leftVacation(User $user) : int
    {
        $fullVacation = 0;
        $date = Carbon::createFromDate(date('Y'));
        $allVacationYear = self::getAllVacation($user->id, $date->copy(), [Vacation::TYPE_TARIFF])->all();
        foreach ($allVacationYear as $vacation) {
            if ($vacation->isApproved()) {
                $fullVacation += $vacation->countVacationDaysTariff();
            }
        }

        return (self::experienceUser($user)) - $fullVacation;
    }

    /**
     * @param $id
     * @param $type
     *
     * @return int
     */
    public static function typeVacation($id, $type)
    {
        $date = Carbon::now();
        $startYear = $date->startOfYear()->timestamp;
        $endYear = $date->endOfYear()->timestamp;
        $vacation = Vacation::find()
            ->where([
                'user_id' => $id,
                'type' => $type,
            ])
            ->andWhere(['between', 'start_at', $startYear, $endYear])
            ->andWhere(['between', 'end_at', $startYear, $endYear])
            ->all();
        $finish = 0;
        foreach ($vacation as $item) {
            $finish += $item->countVacationDaysTariff();
        }

        return $finish;
    }

    public function filterVacation($post)
    {
        $events = [];
        $model = Vacation::find()
            ->where(['in', 'user_id', $post['user']])
            ->all();
        foreach ($model as $value) {
            $events[] = $value->modelEventAttribut();
        }

        return $events;
    }

    /**
     *
     * @return array
     *
     */
    public function listEventVacation()
    {
        $query = Vacation::find();

        if (Yii::$app->user->identity->isAllowedToViewStage()) {
            return $this->eventVacation($query->all());
        } else {
            return $this->eventVacation($query->where(['user_id' => Yii::$app->user->id])->all());
        }
    }

    /**
     * @param $vacation
     *
     * @return array
     */
    public function eventVacation($vacations)
    {
        $events = [];

        foreach ($vacations AS $vacation) {
            $events[] = $vacation->modelEventAttribut();
        }

        return $events;
    }

    /**
     * @return \yii2fullcalendar\models\Event
     */
    public function modelEventAttribut()
    {
        $Event = new \yii2fullcalendar\models\Event();
        $Event->id = $this->id;
        $Event->title = Yii::t('app', 'Vacation') . ' - ' . $this->user->fullName;
        $Event->className = 'progress-bar-striped vacationCalendar';
        $Event->color = Vacation::getCurrencyColor()[$this->type];
        $Event->start = date('Y-m-d\TH:i:s\Z', $this->start_at);
        $Event->end = date('Y-m-d\TH:i:s\Z', strtotime(date('Y-m-d', $this->end_at) . "+1 days"));

        return $Event;
    }

    /**
     * @return array
     */
    public function eventAttribute()
    {
        return $Event = [
            'id' => $this->id,
            'user_id' => (int)$this->user_id,
            'type' => (int)$this->type,
            'title' => Yii::t('app', 'Vacation') . ' - ' . $this->user->fullName,
            'start' => date('Y-m-d', $this->start_at),
            'end' => date('Y-m-d', $this->end_at),
            'days' => $this->countVacationDaysTariff(),
        ];
    }

    /**
     * days off in a month
     *
     * @param $month
     * @param $year
     *
     * @return array
     */
    public function weekendDay($month, $year)
    {

        $year = intval($year);
        $month = intval($month);
        $monthDayPointer = Carbon::create($year, $month, 1, 0);
        $weekends = [];

        while ($monthDayPointer->month === $month) {
            if ($monthDayPointer->isWeekend()) {
                $weekends[] = $monthDayPointer->day;
            }
            $monthDayPointer = $monthDayPointer->addDay();
        }

        return $weekends;
    }

    /**
     * @param string $month
     * @return array
     */
    public function holidays(string $month) : array
    {
        $holidays = [];
        $holiday = Holiday::find()->all();
        foreach ($holiday as $item) {
            if ($month === date('n', $item->date)) {
                $holidays[] = [
                    'day' => intval(date('j', $item->date)),
                    'name' => $item->name,
                ];
            }
        }

        return $holidays;
    }

    /**
     * @param $startYear
     * @param $endYear
     *
     * @return array
     */
    public function listVacation($startYear, $endYear)
    {
        $events = [];
        $vacations = Vacation::find()
            ->where(['between', 'start_at', $startYear, $endYear])
            ->orWhere(['between', 'end_at', $startYear, $endYear])
            ->all();
        foreach ($vacations as $vacation) {
            $events[] = $vacation->eventAttribute();
        }

        return $events;
    }

    public function fields()
    {
        $fields = parent::fields();

        return array_merge($fields, [
            'user' => 'user',
        ]);
    }

    /**
     * Return all Vacation by current year order by desc
     * @param integer $userId
     * @param Carbon $date
     * @param array $type
     * @return array $vacations
     */
    public static function getAllVacationOrderByDate($userId, $date, $type)
    {
        return self::getAllVacation($userId, $date, $type)->orderBy(['start_at' => SORT_DESC])->all();
    }
    
    /**
     * @return false|int
     * @throws \Exception
     * @throws \Throwable
     */
    public function delete()
    {
        if (!$this->isAllowedToDelete()) {
            return false;
        }
        $deleteResult = parent::delete();
        if ($deleteResult > 0) {
            $mailer = new Mailer();
            $mailer->sendVacationDeleteMail($this);
        }
        return $deleteResult;
    }

    /**
     * @return bool
     */
    function isApproved()
    {
        return $this->approve === Vacation::APPROVE_YES;
    }

    /**
     * @return bool
     */
    function isNotApproved()
    {
        return $this->approve === Vacation::APPROVE_NO;
    }

    /**
     * Checks who and under what conditions has permission to remove vacation
     * @return bool
     */
    public function isAllowedToDelete()
    {
        if (\Yii::$app->user->getIdentity()->isRole(User::ROLE_SUPER_ADMIN) ||
            \Yii::$app->user->getIdentity()->isRole(User::ROLE_ADMIN)) {
            return true;
        }
        if (Yii::$app->user->id === $this->user->id && $this->isNotApproved()) {
            return true;
        }
        return false;
    }

    /**
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        $isNewRecord = $this->getIsNewRecord();
        $save = parent::save($runValidation, $attributeNames);
        $mailer = new Mailer();
        if ($save) {
            $isNewRecord ? $mailer->sendVacationCreateMail($this) : $mailer->sendVacationUpdateMail($this);
        }
        return $save;
    }

    /**
     * Checks if vacation is selected for certain days
     * @param $attribute
     * @return bool|void
     */
    public function validationStartDate($attribute)
    {
        $vacations = $this->getAllVacationInRange($this->user_id, [Vacation::TYPE_TARIFF, Vacation::TYPE_SICK_LEAVE, Vacation::TYPE_NOT_PAID]);
        foreach ($vacations as $vacation) {
            if (($vacation->id != $this->id) && ($this->start_at >= $vacation->start_at || $this->end_at <= $vacation->end_at)) {
                return $this->addError($attribute, Yii::t('app', 'You have already chosen a vacation for these days'));
            }
        }
    }

    /**
     * Selects all vacation in the given range
     * @param integer $userId
     * @param array $type
     * @return array
     */
    private function getAllVacationInRange($userId, $type) : array
    {
        return Vacation::find()
            ->where([
                'user_id' => $userId,
                'type' => $type,
            ])
            ->andWhere(['between', 'start_at', $this->start_at, $this->end_at])
            ->orWhere(['between', 'end_at', $this->start_at, $this->end_at])
            ->all();
    }
}
