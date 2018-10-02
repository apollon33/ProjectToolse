<?php

namespace api\controllers;

use common\models\User;
use Yii;
use yii\helpers\Url;
use yii\rest\Controller;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use common\models\UserSearch;

use common\access\AccessManager;


class UserController extends Controller
{
    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['authMethods'] = [
            HttpBearerAuth::className(),
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['permission'],
                    'roles' => ['@'],
                ],
                [
                    'allow' => true,
                    'actions' => ['index', 'view', 'archive'],
                    'roles' => ['core.backend.user:' . AccessManager::VIEW],
                ],
                [
                    'allow' => true,
                    'actions' => ['update', 'archive-restore', 'delete'],
                    'roles' => ['core.backend.user:' . AccessManager::UPDATE],
                ],
                [
                    'allow' => true,
                    'actions' => ['create'],
                    'roles' => ['core.backend.user:' . AccessManager::CREATE],
                ],
                [
                    'allow' => true,
                    'actions' => ['delete', 'archive-delete'],
                    'roles' => ['core.backend.user:' . AccessManager::DELETE],
                ],

            ],
        ];
        return $behaviors;
    }

    /**
     * Displays a list of users sorted by parameters
     * @return \yii\data\ActiveDataProvider
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            if (!empty(Yii::$app->request->queryParams['page'])) {
                $dataProvider = $searchModel->search(array_merge(Yii::$app->request->queryParams, [
                    'page' => 1 + (int)(Yii::$app->request->queryParams['page'] / Yii::$app->request->get('pageSize', $dataProvider->pagination->pageSize))
                ]));
            }

        return $dataProvider;

    }

    /**
     * @param $id
     * @return User
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $user = User::find()->where(['id' => $id])->one();
        if ($user !== null) {
            return $user;
        } else {
            throw new NotFoundHttpException('User is not found!');
        }
    }

    /**
     * @return User
     * @throws ServerErrorHttpException
     */
    public function actionCreate()
    {
        $model = new User();
        $model->loadDefaultValues();
        $model->scenario = User::SCENARIO_SAVE_USER;
        $model->verified = true;
        $model->load(Yii::$app->request->getBodyParams(), '');
        $model->auth_key = \Yii::$app->security->generateRandomString();
        $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password);
        $model->access_token = \Yii::$app->security->generateRandomString();

        if ($model->save()) {
            return $model;
        } elseif ($model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object.');
        }
    }

    /**
     * @param $id
     * @return User
     * @throws ForbiddenHttpException
     * @throws NotAcceptableHttpException
     */
    public function actionUpdate($id)
    {
        $user = User::find()->where(['id' => $id])->one();

        if ($user->id !== Yii::$app->user->id && !Yii::$app->user->can('core.backend.admin:' . AccessManager::UPDATE)) {
            throw new ForbiddenHttpException('Access is denied!');
        }

        $user->scenario = User::SCENARIO_SAVE_USER;
        $user->load(Yii::$app->request->getBodyParams(), '');

        if ($user->save()) {
            return $user;
        } elseif ($user->hasErrors()) {
            throw new NotAcceptableHttpException('Failed to update the object.');
        }

    }

    /**
     * Creates a remote status for the user
     * @param $id
     * @return array
     * @throws ForbiddenHttpException
     * @throws NotAcceptableHttpException
     */
    public function actionDelete($id)
    {
        $user = User::findOne($id);

        if ($user->id !== Yii::$app->user->id && !Yii::$app->user->can('core.backend.admin:' . AccessManager::UPDATE)) {
            throw new ForbiddenHttpException('Access is denied!');
        }
        $user->deleted = User::DELETED_YES;
        $user->scenario = User::SCENARIO_SAVE_USER;

        if ($user->save()) {
            return [
                'status' => true,
            ];
        } else {
            throw new NotAcceptableHttpException('Failed to update the object.');
        }
    }

    /**
     * Displays remote users
     * @return \yii\data\ActiveDataProvider
     */
    public function actionArchive()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->searchTrash(Yii::$app->request->queryParams);

        if (!empty(Yii::$app->request->queryParams['page'])) {
            $dataProvider = $searchModel->search(array_merge(Yii::$app->request->queryParams, [
                'page' => 1 + (int)(Yii::$app->request->queryParams['page'] / Yii::$app->request->get('pageSize', $dataProvider->pagination->pageSize))
            ]));
        }

        return $dataProvider;
    }

    /**
     * Removes the user
     * @param $id
     * @return array
     * @throws NotAcceptableHttpException
     */
    public function actionArchiveDelete($id)
    {
        $user = User::findOne($id);

        if ($user->id !== Yii::$app->user->id && ! Yii::$app->user->can('core.backend.admin:' . AccessManager::UPDATE)) {
            throw new ForbiddenHttpException('Access is denied!');
        }
        if ($user->deleted == User::DELETED_YES && $user->delete()) {
            return [
                'status' => true,
            ];
        } else {
            throw new NotAcceptableHttpException('Error while deleting');
        }
    }

    /**
     * @param $id
     * @return array
     * @throws NotAcceptableHttpException
     */
    public function actionArchiveRestore($id)
    {
        $user = User::findOne($id);
        $user->scenario = User::SCENARIO_SAVE_USER;
        $user->deleted = User::DELETED_NO;

        if ($user->save()) {
            return [
                'status' => true,
            ];
        } else {
            throw new NotAcceptableHttpException('Unable to recover');
        }
    }

    /**
     * Displays permissions on actions with users
     * @return array
     */
    public function actionPermission()
    {
        return [
            'GET /user' => Yii::$app->user->can('core.backend.user:' . AccessManager::VIEW),
            'GET /user/0' => Yii::$app->user->can('core.backend.user:' . AccessManager::VIEW),
            'PUT /user/0' => Yii::$app->user->can('core.backend.user:' . AccessManager::UPDATE),
            'POST /user' => Yii::$app->user->can('core.backend.user:' . AccessManager::CREATE),
            'GET /user/archive' => Yii::$app->user->can('core.backend.user:' . AccessManager::VIEW),
            'PUT /user/restore/0' => Yii::$app->user->can('core.backend.user:' . AccessManager::DELETE),
            'PUT /user/archive/0' => Yii::$app->user->can('core.backend.user:' . AccessManager::DELETE),
            'DELETE /user/0' => Yii::$app->user->can('core.backend.user:' . AccessManager::DELETE),
        ];
    }

}