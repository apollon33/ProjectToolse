<?php

namespace modules\vacation\models;

use common\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\vacation\models\Vacation;
use common\behaviors\FilterBehavior;
use yii\data\Pagination;

/**
 * VacationSearch represents the model behind the search form about `modules\vacation\models\Vacation`.
 */
class VacationSearch extends Vacation
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

    public $start_at_from;
    public $start_at_to;
    public $fullName;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'type'], 'integer'],
            [['start_at_from', 'start_at_to',], 'date' ,'format'=>'php:U'],
            [['fullName'], 'safe'],
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
        $query = Vacation::find();

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


        $this->loadAttributes($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        /*$query->andFilterWhere([
            'id' => $this->id,
            'id_user' => $this->id_user,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
        ]);*/
        $query->andFilterWhere([
            'vacation.id' => $this->id,
            'user_id' => $this->user_id,
            'type' => $this->type,
        ]);

        //$var=User::find()->select('id')->where(['first_name'=>$this->fullName])->one();

        $query->andFilterWhere(['like', 'user_id', $this->user_id])
            ->andFilterWhere(['>=', 'start_at', $this->start_at_from ? $this->start_at_from : null])
            ->andFilterWhere(['<=', 'end_at', $this->start_at_to ? $this->start_at_to : null]);

        $dataProvider->sort->attributes['user_id'] = [
            'asc' => ['user.fullName' => SORT_ASC],
            'desc' => ['user.fullName' => SORT_DESC],
        ];

        return $dataProvider;
    }
}
