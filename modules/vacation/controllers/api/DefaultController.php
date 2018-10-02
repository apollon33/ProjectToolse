<?php


namespace modules\vacation\controllers\api;


use modules\vacation\models\Vacation;
use modules\vacation\models\VacationSearch;
use yii\helpers\Url;
use yii\rest\Controller;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use Yii;
use yii\web\ServerErrorHttpException;

use common\access\AccessManager;


class DefaultController extends Controller
{
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
                    'actions' => ['index', 'view'],
                    'roles' => ['vacation.backend.default:' . AccessManager::VIEW],
                ],
                [
                    'allow' => true,
                    'actions' => ['update'],
                    'roles' => ['vacation.backend.default:' . AccessManager::UPDATE],
                ],
                [
                    'allow' => true,
                    'actions' => ['create'],
                    'roles' => ['vacation.backend.default:' . AccessManager::CREATE],
                ],
                [
                    'allow' => true,
                    'actions' => ['delete'],
                    'roles' => ['vacation.backend.default:' . AccessManager::DELETE],
                ],

            ],
        ];
        return $behaviors;
    }

    public function actionIndex()
    {
        $searchModel = new VacationSearch();
        $query = $searchModel->search(Yii::$app->request->queryParams);

        if (!empty(Yii::$app->request->queryParams['page'])) {
            $query = $searchModel->search(array_merge(Yii::$app->request->queryParams, [
                'page' => 1 + (int)(Yii::$app->request->queryParams['page'] / Yii::$app->request->get('pageSize', $query->pagination->pageSize))
            ]));
        }

        return $query;
    }

    public function actionView($id)
    {
        $vacation = Vacation::findOne($id);

        if ($vacation === null) {
            throw new NotFoundHttpException('Vacation is not found!');
        }
        return $vacation;
    }

    public function actionCreate()
    {
        $model = new Vacation();
        $model->loadDefaultValues();
        $model->load(Yii::$app->request->getBodyParams(), '');

        if ($model->save()) {
            return $model;
        } else {
            return [
                'errors' => $model->errors,
            ];
        }
    }

    public function actionUpdate($id)
    {
        $vacation = Vacation::findOne($id);

        if ($vacation === null) {
            throw new NotFoundHttpException('Vacation is not found!');
        }

        $vacation->load(Yii::$app->request->getBodyParams(), '');

        if ($vacation->save()) {
            return $vacation;
        } else {
            return [
                'errors' => $vacation->errors,
            ];
        }
    }

    public function actionDelete($id)
    {
        $vacation = Vacation::findOne($id);

        if ($vacation === null) {
            throw new NotFoundHttpException('Vacation is not found!');
        }

        if ($vacation->delete()) {
            return [
                'status' => true,
            ];
        } else {
            throw new ServerErrorHttpException('Failed to delete object.');
        }
    }

    /**
     * Displays permissions on actions with vacations
     * @return array
     */
    public function actionPermission()
    {
        return [
            'GET /vacation' => Yii::$app->user->can('vacation.backend.default:' . AccessManager::VIEW),
            'GET /vacation' => Yii::$app->user->can('vacation.backend.default:' . AccessManager::VIEW),
            'PUT /vacation/0' => Yii::$app->user->can('vacation.backend.default:' . AccessManager::UPDATE),
            'POST /vacation/0' => Yii::$app->user->can('vacation.backend.default:' . AccessManager::CREATE),
            'DELETE /vacation/0' => Yii::$app->user->can('vacation.backend.default:' . AccessManager::DELETE),
        ];

    }
}