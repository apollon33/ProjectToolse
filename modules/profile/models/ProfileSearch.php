<?php

namespace modules\profile\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\behaviors\FilterBehavior;
use modules\profile\models\Profile;

/**
 * ProfileSearch represents the model behind the search form about `modules\profile\models\Profile`.
 */
class ProfileSearch extends Profile
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

    public $rate_from;
    public $rate_to;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'first_name', 'last_name', 'login', 'password', 'email', 'email_password', 'skype', 'skype_password', 'description', 'verification', 'note', 'fullName'], 'safe'],
            [['rate'], 'number'],
            [['rate_from', 'rate_to'], 'number'],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['fullName']);
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
        $query = Profile::find();

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
        /*$query->andFilterWhere([
            'id' => $this->id,
            'rate' => $this->rate,
        ]);*/

        $query->andFilterWhere([
            'profile.id' => $this->id,
            'fullName' => $this->first_name . ' ' . $this->last_name,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'login', $this->login])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'email_password', $this->email_password])
            ->andFilterWhere(['like', 'skype', $this->skype])
            ->andFilterWhere(['like', 'skype_password', $this->skype_password])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'verification', $this->verification])
            ->andFilterWhere(['like', 'note', $this->note]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['or', ['like', 'first_name', $this->fullName], ['like', 'last_name', $this->fullName]])
            ->andFilterWhere(['>=', 'rate', $this->rate_from ? $this->rate_from : null])
            ->andFilterWhere(['<=', 'rate', $this->rate_to ? $this->rate_to : null]);

        $dataProvider->sort->attributes['fullName'] = [
            'asc' => ['first_name' => SORT_ASC, 'last_name' => SORT_ASC],
            'desc' => ['first_name' => SORT_DESC, 'last_name' => SORT_DESC],
        ];

        return $dataProvider;
    }
}
