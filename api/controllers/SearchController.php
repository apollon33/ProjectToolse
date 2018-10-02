<?php

namespace api\controllers;

use api\services\SearchService;
use common\access\AccessManager;
use yii\rest\Controller;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;

class SearchController extends Controller
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
                    'actions' => ['index'],
                    'roles' => ['core.backend.user:' . AccessManager::VIEW],
                ],
            ],
        ];
        return $behaviors;
    }

    /**
     * Search on users, documents, positions, departments and holidays.
     *
     * @param array $params
     *
     * @return array
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;

        $searchService = new SearchService();
        $searchModel = $searchService->getSearchModelByParams($request->queryParams);

        return $searchService->getSearchResult($searchModel, $request->queryParams);
    }
}