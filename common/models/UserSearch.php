<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\behaviors\FilterBehavior;
use modules\countingtime\models\CountingTime;

/**
 * UserSearch represents the model behind the search form about `modules\user\models\User`.
 */
class UserSearch extends User
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
    public $date_login_from;
    public $date_login_to;
    public $date_receipt_from;
    public $date_receipt_to;
    public $date_dismiss_from;
    public $date_dismiss_to;
    public $roles;

    public $hour_start;
    public $hour_end;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'verified', 'active', 'created_at', 'updated_at', 'last_login_at', 'position_id'], 'integer'],
            [['username', 'auth_key', 'password_hash', 'password_reset_token', 'email', 'first_name', 'deleted', 'last_name', 'avatar', 'fullName', 'searchAll'], 'safe'],
            [['date_from', 'date_to'], 'date' ,'format'=>'php:U'],
            [['hour_start', 'hour_end'], 'date' ,'format'=>'php:U'],
            [['date_receipt_from', 'date_receipt_to', 'date_dismiss_from', 'date_dismiss_to'], 'date' ,'format'=>'php:U'],
            [['date_login_from', 'date_login_to'], 'date' ,'format'=>'php:U'],
            [['roles'], 'string'],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['fullName', 'searchAll']);
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
        $query = User::find()->with('roles');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_ASC,
                ]
            ],
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

        $query->andFilterWhere([
            '{{%user}}.id' => $this->id,
            'deleted' => User::DELETED_NO, //filter users by not deleted
            'verified' => $this->verified,
            'active' => $this->active,
            'position_id' => $this->position_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'last_login_at' => $this->last_login_at,
            'fullName' => $this->first_name . ' ' . $this->last_name,
        ])->joinWith([
            'country' => function($query) {
                $query->from(['country' => '{{%country}}']);
            },
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'avatar', $this->avatar])
            ->andFilterWhere(['or', ['like', 'first_name', $this->fullName], ['like', 'last_name', $this->fullName]])
            ->andFilterWhere(['or', ['like', 'first_name', $this->searchAll], ['like', 'last_name', $this->searchAll], ['like', 'email', $this->searchAll], ['like', 'username', $this->searchAll], ['like', 'middle_name', $this->searchAll]])
            ->andFilterWhere(['>=', 'created_at', $this->date_from ? $this->date_from : null])
            ->andFilterWhere(['<=', 'created_at', $this->date_to ? $this->date_to : null])
            ->andFilterWhere(['>=', 'date_receipt', $this->date_receipt_from ? $this->date_receipt_from : null])
            ->andFilterWhere(['<=', 'date_receipt', $this->date_receipt_to ? $this->date_receipt_to : null])
            ->andFilterWhere(['>=', 'date_dismissal', $this->date_dismiss_from ? $this->date_dismiss_from : null])
            ->andFilterWhere(['<=', 'date_dismissal', $this->date_dismiss_to ? $this->date_dismiss_to : null])
            ->andFilterWhere(['>=', 'last_login_at', $this->date_login_from ? $this->date_login_from : null])
            ->andFilterWhere(['<=', 'last_login_at', $this->date_login_to ? $this->date_login_to : null]);

        $dataProvider->sort->attributes['fullName'] = [
            'asc' => ['first_name' => SORT_ASC, 'last_name' => SORT_ASC],
            'desc' => ['first_name' => SORT_DESC, 'last_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['searchAll'] = [
            'asc' => ['first_name' => SORT_ASC, 'last_name' => SORT_ASC],
            'desc' => ['first_name' => SORT_DESC, 'last_name' => SORT_DESC],
        ];

        $assignmentTable = Yii::$app->authManager->assignmentTable;

        $dataProvider->sort->attributes['roles'] = [
            'asc' => [$assignmentTable.'.item_name' => SORT_ASC],
            'desc' => [$assignmentTable . '.item_name' => SORT_DESC],
        ];

        return $dataProvider;
    }

    public function searchTrash($params)
    {
        $query = User::find()->with('roles')->where(['deleted'=>User::DELETED_YES]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_ASC,
                ]
            ],
        ]);

        if (!empty($params['pageSize'])) {
            $dataProvider->pagination->pageSize = $params['pageSize'];
        }

        if (!empty($params['page'])) {
            $dataProvider->pagination->setPage($params['page'] - 1, false);
        }

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            '{{%user}}.id' => $this->id,
            'item_name' => $this->roles,
            'verified' => $this->verified,
            'active' => $this->active,
            'position_id' => $this->position_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'last_login_at' => $this->last_login_at,
            'fullName' => $this->first_name . ' ' . $this->last_name,
        ])->joinWith([
            'roles' => function ($query) {
                $query->from(['r' => RoleModel::tableName()]);
            },
            'country' => function($query) {
                $query->from(['country' => '{{%country}}']);
            },
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'avatar', $this->avatar])
            ->andFilterWhere(['or', ['like', 'first_name', $this->fullName], ['like', 'last_name', $this->fullName]])
            ->andFilterWhere(['or', ['like', 'first_name', $this->searchAll], ['like', 'last_name', $this->searchAll], ['like', 'email', $this->searchAll], ['like', 'username', $this->searchAll], ['like', 'middle_name', $this->searchAll]])
            ->andFilterWhere(['>=', 'created_at', $this->date_from ? $this->date_from : null])
            ->andFilterWhere(['<=', 'created_at', $this->date_to ? $this->date_to : null])
            ->andFilterWhere(['>=', 'date_receipt', $this->date_receipt_from ? $this->date_receipt_from : null])
            ->andFilterWhere(['<=', 'date_receipt', $this->date_receipt_to ? $this->date_receipt_to : null])
            ->andFilterWhere(['>=', 'date_dismissal', $this->date_dismiss_from ? $this->date_dismiss_from : null])
            ->andFilterWhere(['<=', 'date_dismissal', $this->date_dismiss_to ? $this->date_dismiss_to : null])
            ->andFilterWhere(['>=', 'last_login_at', $this->date_login_from ? $this->date_login_from : null])
            ->andFilterWhere(['<=', 'last_login_at', $this->date_login_to ? $this->date_login_to : null]);

        $dataProvider->sort->attributes['fullName'] = [
            'asc' => ['first_name' => SORT_ASC, 'last_name' => SORT_ASC],
            'desc' => ['first_name' => SORT_DESC, 'last_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['searchAll'] = [
            'asc' => ['first_name' => SORT_ASC, 'last_name' => SORT_ASC],
            'desc' => ['first_name' => SORT_DESC, 'last_name' => SORT_DESC],
        ];

        $assignmentTable = Yii::$app->authManager->assignmentTable;

        $dataProvider->sort->attributes['roles'] = [
            'asc' => [$assignmentTable.'.item_name' => SORT_ASC],
            'desc' => [$assignmentTable . '.item_name' => SORT_DESC],
        ];

        return $dataProvider;
    }


    /**
     * Interval selection hours
     * @param integer $id
     * @return string
     */
    public function getTime($id)
    {
        $countingtime=new CountingTime();
        $this->load(Yii::$app->request->queryParams);
        return $countingtime->getTime($id, $this->hour_start, $this->hour_end);
    }

}
