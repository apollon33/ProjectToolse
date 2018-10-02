<?php

namespace modules\actioncalendar\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\behaviors\FilterBehavior;

/**
 * EventSearch represents the model behind the search form about `modules\actioncalendar\models\Event`.
 */
class EventSearch extends Event
{
    /**
     *
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
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'active'], 'integer'],
            [['name'], 'safe'],
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
        $query = Event::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ]
        ]);

//        $this->load($params);
        $this->loadAttributes($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'active' => $this->active,
        ]);


        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['>=', 'start_at', $this->start_at_from ? $this->start_at_from : null])
            ->andFilterWhere(['<=', 'end_at', $this->start_at_to ? $this->start_at_to : null]);

        return $dataProvider;
    }
}
