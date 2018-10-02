<?php

namespace modules\project\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\behaviors\FilterBehavior;
use modules\project\models\Project;

/**
 * PeojectSearch represents the model behind the search form about `modules\project\models\Project`.
 */
class ProjectSearch extends Project
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
    /**
     * @inheritdoc
     */
    public $salary_from;
    public $salary_to;

    public function rules()
    {
        return [
            [['id', 'client_id', 'profile_id'], 'integer'],
            [['name', 'description'], 'safe'],
            [['rate'], 'number'],
            [['salary_from', 'salary_to'], 'number'],
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
        $query = Project::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->loadAttributes($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'project.id' => $this->id,
            'rate' => $this->rate,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'client_id', $this->client_id])
            ->andFilterWhere(['like', 'profile_id', $this->profile_id])
            ->andFilterWhere(['like', 'description', $this->description]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['>=', 'rate', $this->salary_from ? $this->salary_from : null])
            ->andFilterWhere(['<=', 'rate', $this->salary_to ? $this->salary_to : null]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description]);



        return $dataProvider;
    }
}
