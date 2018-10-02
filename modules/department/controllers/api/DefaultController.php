<?php

namespace modules\department\controllers\api;

use modules\department\models\Department;
use modules\department\models\DepartmentSearch;
use yii\helpers\Url;
use yii\rest\Controller;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use Yii;

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
                    'roles' => ['department.backend.default:' . AccessManager::VIEW],
                ],
                [
                    'allow' => true,
                    'actions' => ['update'],
                    'roles' => ['department.backend.default:' . AccessManager::UPDATE],
                ],
                [
                    'allow' => true,
                    'actions' => ['create'],
                    'roles' => ['department.backend.default:' . AccessManager::CREATE],
                ],
                [
                    'allow' => true,
                    'actions' => ['delete'],
                    'roles' => ['department.backend.default:' . AccessManager::DELETE],
                ],

            ],
        ];
        return $behaviors;
    }

    public function actionIndex()
    {
        $searchModel = new DepartmentSearch();
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
        $department = Department::findOne($id);
        if ($department === null) {
            throw new NotFoundHttpException('Department is not found!');
        }
        return $department;
    }

    public function actionCreate()
    {
        $model = new Department();
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
        $department = Department::findOne($id);
        $department->load(\Yii::$app->request->getBodyParams(),'');

        if ($department->save()) {
            return $department;
        } else {
            return [
                'errors' => $department->errors,
            ];
        }
    }

    public function actionDelete($id)
    {
        $department = Department::findOne($id);

        if ($department->delete()) {
            return [
                'status' => true,
            ];
        } else {
            throw new ServerErrorHttpException('Failed to delete object.');
        }
    }

    /**
     * Displays permissions on actions with departments
     * @return array
     */
    public function actionPermission()
    {
        return [
            'GET /department' => Yii::$app->user->can('department.backend.default:' . AccessManager::VIEW),
            'GET /department/0' => Yii::$app->user->can('department.backend.default:' . AccessManager::VIEW),
            'PUT /department/0' => Yii::$app->user->can('department.backend.default:' . AccessManager::UPDATE),
            'POST /department' => Yii::$app->user->can('department.backend.default:' . AccessManager::CREATE),
            'DELETE /department/0' => Yii::$app->user->can('department.backend.default:' . AccessManager::DELETE),
        ];
    }
}