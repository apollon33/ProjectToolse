<?php

namespace modules\buildingprocess\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\buildingprocess\models\ProcessTemplate;

/**
 * BuildingProcessSearch represents the model behind the search form about
 * `modules\buildingprocess\models\ProcessTemplate`.
 */
class ProcessTemplateSearch extends ProcessTemplate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'display', 'create_folder', 'parent'], 'integer'],
            [['title', 'type', 'description'], 'safe'],
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
        $query = ProcessTemplate::find()->where(['parent' => null]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!empty($params['pageSize'])) {
            $dataProvider->pagination->pageSize = $params['pageSize'];
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
            'display' => $this->display,
            'create_folder' => $this->create_folder,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }

    /**
     * @param int
     *
     * @return ActiveDataProvider
     */
    public function searchStages($parent)
    {
        $query = ProcessTemplate::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'sorting' => SORT_ASC,
                ],
            ],
        ]);
        $query->andFilterWhere([
            'parent' => $parent,
        ]);

        return $dataProvider;
    }
}
