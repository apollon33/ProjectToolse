<?php

namespace modules\position\controllers\api;

use modules\position\models\Position;
use modules\position\models\PositionSearch;
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
                    'roles' => ['position.backend.default:' . AccessManager::VIEW],
                ],
                [
                    'allow' => true,
                    'actions' => ['update'],
                    'roles' => ['position.backend.default:' . AccessManager::UPDATE],
                ],
                [
                    'allow' => true,
                    'actions' => ['create'],
                    'roles' => ['position.backend.default:' . AccessManager::CREATE],
                ],
                [
                    'allow' => true,
                    'actions' => ['delete'],
                    'roles' => ['position.backend.default:' . AccessManager::DELETE],
                ],

            ],
        ];
        return $behaviors;
    }

    public function actionIndex()
    {
        $searchModel = new PositionSearch();
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
        $position = Position::findOne($id);

        if ($position === null) {
            throw new NotFoundHttpException('Position is not found!');
        }
        return $position;
    }

    public function actionCreate()
    {
        $model = new Position();
        $model->loadDefaultValues();
        $model->load(\Yii::$app->request->getBodyParams(), '');

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
        $position = Position::findOne($id);
        $position->load(Yii::$app->request->getBodyParams(), '');

        if ($position->save()) {
            return $position;
        } else {
            return [
                'errors' => $position->errors,
            ];
        }
    }

    public function actionDelete($id)
    {
        $position = Position::findOne($id);

        if ($position->delete()) {
            return [
                'status' => true,
            ];

        } else {
            throw new ServerErrorHttpException('Failed to delete object.');
        }
    }

    /**
     * Displays permissions on actions with positions
     * @return array
     */
    public function actionPermission()
    {
        return [
            'GET /position' => Yii::$app->user->can('position.backend.default:' . AccessManager::VIEW),
            'GET /position/0' => Yii::$app->user->can('position.backend.default:' . AccessManager::VIEW),
            'PUT /position/0' => Yii::$app->user->can('position.backend.default:' . AccessManager::UPDATE),
            'POST /position' => Yii::$app->user->can('position.backend.default:' . AccessManager::CREATE),
            'DELETE /position/0' => Yii::$app->user->can('position.backend.default:' . AccessManager::DELETE),
        ];
    }
}
