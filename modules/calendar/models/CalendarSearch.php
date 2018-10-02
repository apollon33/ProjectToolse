<?php

namespace modules\calendar\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\behaviors\FilterBehavior;
use modules\countingtime\models\CountingTime;

/**
 * CalendarSearch represents the model behind the search form about `modules\calendar\models\Calendar`.
 * @property mixed created_by
 */
class CalendarSearch extends Calendar
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            FilterBehavior::className(),
        ];
    }

    public $start_at_from;
    public $start_at_to;
    public $hour_start;
    public $hour_end;

    //public $hour;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'project_id', 'start_at', 'end_at', 'created_at', 'updated_at', 'created_by','actual_time'], 'integer'],
            [['description', 'estimated_time'], 'safe'],
            [['estimate_approval'], 'boolean'],
            [['start_at_from', 'start_at_to'], 'date' ,'format'=>'php:U'],
            [['hour_start', 'hour_end'], 'date' ,'format'=>'php:U'],
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
//        if(Yii::$app->user->identity->role_id === User::TYPE_SUPER_ADMIN || Yii::$app->user->identity->role_id === User::TYPE_ADMIN) {
            $query = Calendar::find();
//        }
//        else{
//            $query = Calendar::find()->where(['user_id' => Yii::$app->user->id]);
//        }
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ]
        ]);

        $this->loadAttributes($params);

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
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);
        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'user_id', $this->user_id])
            ->andFilterWhere(['like', 'project_id', $this->project_id])
            ->andFilterWhere(['like', 'created_by', $this->created_by])
            ->andFilterWhere(['>=', 'start_at', $this->start_at_from ? $this->start_at_from : null])
            ->andFilterWhere(['<=', 'end_at', $this->start_at_to ? $this->start_at_to : null]);

        return $dataProvider;
    }

    /**
     * Interval selection hours
     * @param integer $id
     * @return string
     */
    public function getTime($id)
    {
        $countingtime = new CountingTime();
        $this->load(Yii::$app->request->queryParams);

        return $countingtime->getTime($id);
    }

}


