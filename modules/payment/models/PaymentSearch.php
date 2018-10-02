<?php

namespace modules\payment\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\behaviors\FilterBehavior;
use modules\payment\models\Payment;

/**
 * PaymentSearch represents the model behind the search form about `modules\payment\models\Payment`.
 */
class PaymentSearch extends Payment
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

    public $date_from;
    public $date_to;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'type', 'created_at', 'updated_at','mounth', 'year'], 'integer'],
            [['amount', 'tax_profit', 'tax_war', 'tax_pension', 'payout'], 'number'],
            [['fullName'], 'safe'],
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
        $query = Payment::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    //'created_at' => SORT_ASC,
                    'mounth' => SORT_DESC,
                    'year' => SORT_DESC,
                ]
            ],
        ]);


        if (!empty($params['pageSize'])) {
            $dataProvider->pagination->pageSize = $params['pageSize'];
        }

        $this->loadAttributes($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'payment.id' => $this->id,
            'user_id' => $this->user_id,
            'payment.created_at' => $this->created_at,
            'mounth' => $this->mounth,
            'year' => $this->year,
            'type' => $this->type,
            'amount' => $this->amount,
            'tax_profit' => $this->tax_profit,
            'tax_war' => $this->tax_war,
            'tax_pension' => $this->tax_pension,
            'payout' => $this->payout
        ]);

        $query->andFilterWhere(['like', 'payment.id', $this->id])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'user_id', $this->user_id])
            ->andFilterWhere(['like', 'amount', $this->amount])
            ->andFilterWhere(['like', 'tax_profit', $this->tax_profit])
            ->andFilterWhere(['like', 'tax_war', $this->tax_war])
            ->andFilterWhere(['like', 'tax_pension', $this->tax_pension])
            ->andFilterWhere(['like', 'payout', $this->payout])
            ->andFilterWhere(['>=', 'payment.created_at', $this->date_from ? $this->date_from : null])
            ->andFilterWhere(['<=', 'payment.created_at', $this->date_to ? $this->date_to : null]);
        
        $dataProvider->sort->attributes['fullName'] = [
            'asc' => ['last_name' => SORT_ASC],
        ];

        return $dataProvider;
    }
}
