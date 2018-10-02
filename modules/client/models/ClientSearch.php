<?php

namespace modules\client\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\behaviors\FilterBehavior;
use modules\client\models\Client;

/**
 * ClientSearch represents the model behind the search form about `modules\client\models\Client`.
 */
class ClientSearch extends Client
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
    public function rules()
    {
        return [
            [['id', 'country_id'], 'integer'],
            [['client_name', 'company_name', 'email', 'skype', 'phone', 'description'], 'safe'],
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
    public function search($params, $deleted = 0)
    {
        $query = Client::find()->where(['deleted' => $deleted]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);



        if (!empty($params['pageSize'])) {
            $dataProvider->pagination->pageSize = $params['pageSize'];
        }

        $this->loadAttributes($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            return $dataProvider;
        }

        $query->andFilterWhere([
            'client.id' => $this->id,
            'client_name' => $this->client_name,
        ]);

        $query
            ->andFilterWhere(['like', 'client_name', $this->client_name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'skype', $this->skype])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'description', $this->description]);

        $dataProvider->sort->attributes['client_name'] = [
            'asc' => ['client_name' => SORT_ASC],
            'desc' => ['client_name' => SORT_DESC],
        ];

        return $dataProvider;
    }
}
