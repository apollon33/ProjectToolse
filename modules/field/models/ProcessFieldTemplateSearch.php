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
            [['id', 'process_id', 'required', 'sorting'], 'integer'],
            [['name', 'type_field'], 'safe'],
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
            'sort' => [
                'defaultOrder' => [
                    'sorting' => SORT_ASC,
                ]
            ]
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'process_id' => $process_id,
        ]);

        /*$query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'description', $this->description]);*/

        return $dataProvider;
    }
}
