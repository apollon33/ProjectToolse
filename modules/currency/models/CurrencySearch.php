<?php

namespace modules\currency\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\currency\models\Currency;

/**
 * CurrencySearch represents the model behind the search form about `modules\currency\models\Currency`.
 */
class CurrencySearch extends Currency
{

    public $date_from;
    public $date_to;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'date'], 'integer'],
            [['key_digital', 'key_letter', 'name'], 'safe'],
            [['rate'], 'number'],
            [['date_from', 'date_to'], 'date' ,'format'=>'php:U'],
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
        $query = Currency::find();

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
            'date' => $this->date,
            'key_digital' => $this->key_digital,
            'key_letter' => $this->key_letter,

        ]);

        $query->andFilterWhere(['like', 'key_digital', $this->key_digital])
            ->andFilterWhere(['like', 'key_letter', $this->key_letter])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'rate', $this->rate])
            ->andFilterWhere(['>=', 'date', $this->date_from ? $this->date_from : null])
            ->andFilterWhere(['<=', 'date', $this->date_to ? $this->date_to : null]);


        return $dataProvider;
    }
}
