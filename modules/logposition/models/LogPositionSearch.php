<?php

namespace modules\logposition\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\logposition\models\LogPosition;

/**
 * LogPositionSearch represents the model behind the search form about `modules\logposition\models\LogPosition`.
 */
class LogPositionSearch extends LogPosition
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'position_id', 'created_at'], 'integer'],
            [['description'], 'safe'],
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
        $query = LogPosition::find()->where(['user_id'=>$params['id']]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['created_at' => SORT_DESC]]
        ]);

        if (!empty($params['pageSize'])) {
            $dataProvider->pagination->pageSize = 5;
        }

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
            'position_id' => $this->position_id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
