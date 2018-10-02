<?php

namespace modules\field\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\field\models\ProcessFieldTemplate;

/**
 * BuildingProcessSearch represents the model behind the search form about `modules\buildingprocess\models\BuildingProcess`.
 */
class ProcessFieldTemplateSearch extends ProcessFieldTemplate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['process_id', 'field_id'], 'integer'],
            [['data'], 'safe'],
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
    public function search($process_id)
    {
        $query = ProcessFieldTemplate::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'process_id' => $this->process_id,
            'field_id' => $this->field_id,
        ]);

        $query->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
