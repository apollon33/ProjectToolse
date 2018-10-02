<?php

namespace modules\activity\models;

use common\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ActivitySearch represents the model behind the search form about `modules\activity\models\Activity`.
 */
class ActivitySearch extends Activity
{
    const IMAGE_STATUS_EXIST = 1;
    const IMAGE_STATUS_EMPTY = 2;

    public $date;
    public $date_from;
    public $date_to;

    public $screen;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'project_id', 'screen', 'keyboard_activity_percent', 'mouse_activity_percent', 'interval', 'created_at', 'updated_at'], 'integer'],
            [['target_window', 'description'], 'string'],
            [['date'], 'safe'],
            [['date_from', 'date_to'], 'date', 'format' => 'php:U'],
        ];
    }

    public static function getScreenStatuses()
    {
        return [
            //self::IMAGE_STATUS_ALL => Yii::t('app', 'All'),
            self::IMAGE_STATUS_EXIST => Yii::t('app', 'Exist'),
            self::IMAGE_STATUS_EMPTY => Yii::t('app', 'Empty'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Activity::find()
            ->with('user')
            ->with('project');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'project_id' => $this->project_id,
            'keyboard_activity_percent' => $this->keyboard_activity_percent,
            'mouse_activity_percent' => $this->mouse_activity_percent,
            'interval' => $this->interval,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        if ($this->screen == self::IMAGE_STATUS_EXIST) {
            $query->andWhere(['not', ['screenshot' => null]]);
        } elseif ($this->screen == self::IMAGE_STATUS_EMPTY) {
            $query->andWhere(['screenshot' => null]);
        }

        // @todo perepisat !111
        if (!Yii::$app->user->isGuest && Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId()) === 1) {//User::TYPE_DEVELOPER) {
            $query->andWhere(['user_id' => Yii::$app->user->id]);
        }

        $query->andFilterWhere(['like', 'target_window', $this->target_window]);

        $query->andFilterWhere(['>=', 'created_at', $this->date_from ? $this->date_from : null])
            ->andFilterWhere(['<=', 'created_at', $this->date_to ? $this->date_to : null]);

        if (!empty($this->date)) {
            $query->andFilterWhere(['>=', 'created_at', strtotime($this->date)])
                ->andFilterWhere(['<=', 'created_at', strtotime($this->date . ' +1 day')]);
        }

        return $dataProvider;
    }

    /**
     * @param array $params
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getList($params = [])
    {
        $query = Activity::find()
            ->with('user')
            ->with('project');

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $query->all();
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'project_id' => $this->project_id,
            'keyboard_activity_percent' => $this->keyboard_activity_percent,
            'mouse_activity_percent' => $this->mouse_activity_percent,
            'interval' => $this->interval,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        if ($this->screen == self::IMAGE_STATUS_EXIST) {
            $query->andWhere(['not', ['screenshot' => null]]);
        } elseif ($this->screen == self::IMAGE_STATUS_EMPTY) {
            $query->andWhere(['screenshot' => null]);
        }

        // @todo aaaa
//        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role->id == User::TYPE_DEVELOPER) {
//            $query->andWhere(['user_id' => Yii::$app->user->id]);
//        }

        $query->andFilterWhere(['like', 'target_window', $this->target_window]);

        $query->andFilterWhere(['>=', 'created_at', $this->date_from ? $this->date_from : null])
            ->andFilterWhere(['<=', 'created_at', $this->date_to ? $this->date_to : null]);

        if (!empty($this->date)) {
            $query->andFilterWhere(['>=', 'created_at', strtotime($this->date)])
                ->andFilterWhere(['<=', 'created_at', strtotime($this->date . ' +1 day')]);
        }

        return $query->all();
    }

    public function getStatistics($models)
    {
        $results = [];

        $interval = 0;
        $keyboardActivityPercent = 0;
        $mouseActivityPercent = 0;

        foreach($models as $model) {
            $date = Yii::$app->formatter->asDatetime($model->created_at, 'Y-MM-dd');
            if (empty($results[$date])) {
                $results[$date] = [
                    'amount' => 0,
                    'interval_sum' => 0,
                    'keyboard_activity_sum' => 0,
                    'mouse_activity_sum' => 0,
                    'interval' => 0,
                    'keyboard_activity_percent' => 0,
                    'mouse_activity_percent' => 0,
                ];
            }

            $interval += $model->interval;
            $keyboardActivityPercent += $model->keyboard_activity_percent;
            $mouseActivityPercent += $model->mouse_activity_percent;

            $results[$date]['date'] = Yii::$app->formatter->asDatetime($model->created_at, 'php:M d, Y');
            $results[$date]['amount']++;
            $results[$date]['interval_sum'] += $model->interval;
            $results[$date]['keyboard_activity_sum'] += $model->keyboard_activity_percent;
            $results[$date]['mouse_activity_sum'] += $model->mouse_activity_percent;
            $results[$date]['interval'] = Yii::$app->formatter->asDuration($results[$date]['interval_sum']);
            $results[$date]['keyboard_activity_percent'] = round($results[$date]['keyboard_activity_sum'] / $results[$date]['amount'], 2);
            $results[$date]['mouse_activity_percent'] = round($results[$date]['mouse_activity_sum'] / $results[$date]['amount'], 2);
        }

        $results['interval'] = $interval;
        $results['keyboard_activity_percent'] = count($models) > 0 ? $keyboardActivityPercent / count($models) : 0;
        $results['mouse_activity_percent'] = count($models) > 0 ? $mouseActivityPercent / count($models) : 0;

        return $results;
    }
}