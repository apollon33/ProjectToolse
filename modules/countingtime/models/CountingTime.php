<?php

namespace modules\countingtime\models;

use modules\calendar\models\Calendar;
use modules\holiday\models\Holiday;
use common\components\DurationConverter;
use Yii;



class CountingTime
{


    public $dinner=24;//Dinner time
    public $endDay=18;//the end of the day
    public $startDay=9;//the start of the day
    public $min=60;//the number of minutes in an hour
    public $workday=8;
    public $sunday=6;
    public $saturday=5;

    /**
     * Interval selection hours
     * @param integer $id
     * @return string
     */
    public function getTime($id,$hourStart=false,$hourEnd=false,$createdBy=false)
    {
        $hourStart = (!empty($hourStart)  ? $hourStart :false);
        $max = Calendar::find()->orderBy('end_at DESC')->one();
        $hourEnd = (!empty($hourEnd)  ? $hourEnd : !empty($max->end_at)? $max->end_at: false);
        $model = Calendar::find()
            ->where((!empty($createdBy)  ? ['created_by' => $createdBy] :''))
            ->andWhere(['between', 'start_at', $hourStart, $hourEnd])
            ->andWhere(['between', 'end_at', $hourStart, $hourEnd])
            ->andWhere(['user_id' => $id,])
            ->all();
        $hour=0;
        foreach ($model as $item){
            $hour+=$this->jobTime($item->start_at, $item->end_at, $this->workday);
        }
        return DurationConverter::shortDuration(Yii::$app->formatter->asDuration($hour));
    }


    /**
     * Calculation of man-hours.
     * @param integer$start_at
     * @param integer $end_at
     * @param bool $workday
     * @return float|int
     */
    public function  jobTime($startAt,$endAt,$workday=false)
    {
        $s = $this->getStart($startAt);
        $e = $this->getEnd($endAt);
        $weekend = 0;
        $day = date('z', $endAt)-date('z', $startAt);
        $week = date('w', $startAt)-1;
        for($i=0; $i<=$day; $i++){
            if($week === $this->saturday) {
                $weekend++;
            }
            if($week === $this->sunday) {
                $weekend++;
                $week = 0;
            }else{
                $week++;
            }

        }
        $day = $day -/*$weekend-*/1;
        $finih = (((($day*$workday)*$this->min)+($e+$s))*$this->min);
        /*$holiday=Holiday::find()->select('date')->all();
        foreach ($holiday as $item){
            $weekend=date('w',$item->date)-1;
            if($weekend!=$this->saturday&&$weekend!=$this->sunday)
                if($item->date>$start_at && $item->date<$end_at)
                    $finih=$finih-($workday*($this->min*$this->min));
        }*/
        //return ($finih/$this->min);
        return $finih;
    }



    /**
     * The amount of time before the close of business on the day.
     * @param $data
     * @return false|int|string
     */
    public function getStart($data)
    {
        $H = date('H', $data);//hour
        $I = date('i', $data);//min
        if($H < $this->dinner) {//subtract dinner
            $H = $this->endDay - $H - 1;
        } else {
            $H = $this->endDay - $H;
        }
        if($H < 0) {
            $H = 0;
        }
        if($H>$this->workday) {
            $H = $this->workday;
        }
        if($I !== 0 && $H !== $this->workday && $H !== 0) {
            $H = ($H * $this->min) - $I;
        } else {
            $H = ($H * $this->min);
        }
        return $H;
    }

    /**
     * The amount of time from the start of the working day of the day.
     * @param $data
     * @return false|int|string
     */
    public function getEnd($data)
    {
        $H = date('H', $data);//hour
        $I = date('i', $data);//min
        if($H < $this->dinner) {//subtract dinner
            $H = $H-$this->startDay;
        } else {
            $H = $H - ($this->startDay+1);
        }
        if($H < 0) {
            $H = $this->workday;
        }
        if($H > $this->workday) {
            $H = $this->workday;
        }
        if($I !== 0 && $H !== $this->workday && $H != 0) {
            $H = ($H*$this->min)+$I;
        } else {
            $H = ($H*$this->min);
        }

        return $H;

    }



}

