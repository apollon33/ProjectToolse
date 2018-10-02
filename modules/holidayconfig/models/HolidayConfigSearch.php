<?php

namespace modules\holidayconfig\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\holidayconfig\models\HolidayConfig;

/**
 * HolidayConfigSearch represents the model behind the search form about `modules\holidayconfig\models\HolidayConfig`.
 */
class HolidayConfigSearch extends HolidayConfig
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'month', 'day'], 'integer'],
            [['name'], 'safe'],
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
        $query = HolidayConfig::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'month' => $this->month,
            'day' => $this->day,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
