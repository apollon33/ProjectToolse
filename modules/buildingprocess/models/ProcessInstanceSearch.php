<?php

namespace modules\buildingprocess\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\buildingprocess\models\ProcessInstance;

/**
 * BuildingProcessSearch represents the model behind the search form about
 * `modules\buildingprocess\models\BuildingProcess`.
 */
class ProcessInstanceSearch extends ProcessInstance
{
    public $date_from;
    public $date_to;
    public $update_from;
    public $update_to;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['process_id', 'created_at', 'updated_at', 'owner'], 'integer'],
            [['name'], 'safe'],
            [['date_from', 'date_to'], 'date', 'format' => 'php:U'],
            [['update_from', 'update_to'], 'date', 'format' => 'php:U'],
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
        $query = ProcessInstance::getUserDealsQueryByProcessTemplateType(Yii::$app->request->get('type'));

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!empty($params['pageSize'])) {
            $dataProvider->pagination->pageSize = $params['pageSize'];
        }
        if (!empty($params['page'])) {
            $dataProvider->pagination->setPage($params['page'] - 1, false);
        }
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'deal.process_id' => $this->process_id,
            'deal.created_at' => $this->created_at,
            'deal.updated_at' => $this->updated_at,
        ])->joinWith([
            'processFieldInstance' => function($query) {
                $query->from(['process_field_instance' => '{{%process_field_instance}}']);
            }
        ]);

        $query
            ->andFilterWhere(['like', 'process_field_instance.data', $this->name])
            ->andFilterWhere(['like', 'deal.owner', $this->owner])
            ->andFilterWhere(['>=', 'deal.created_at', $this->date_from ? $this->date_from : null])
            ->andFilterWhere(['<=', 'deal.created_at', $this->date_to ? $this->date_to : null])
            ->andFilterWhere(['>=', 'deal.updated_at', $this->update_from ? $this->update_from : null])
            ->andFilterWhere(['<=', 'deal.updated_at', $this->update_to ? $this->update_to : null])
            ->orderBy('updated_at DESC');

        return $dataProvider;
    }
}
