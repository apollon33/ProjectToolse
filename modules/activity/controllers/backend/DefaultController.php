<?php

namespace modules\activity\controllers\backend;

use common\controllers\BaseController;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use modules\activity\models\Activity;
use modules\activity\models\ActivitySearch;

use common\access\AccessManager;

/**
 * DefaultController implements the CRUD actions for Activity model.
 */
class DefaultController extends BaseController
{

    public function permissionMapping()
    {
        return [
            'index' => AccessManager::VIEW,
            'statistics' => AccessManager::VIEW,
            'update' => AccessManager::UPDATE,
            'create' => AccessManager::CREATE,
            'delete' => AccessManager::DELETE,
        ];
    }

    /**
     * Lists all Activity models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ActivitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Activity models.
     * @return mixed
     */
    public function actionStatistics()
    {
        $searchModel = new ActivitySearch();
        $searchModel->user_id = Yii::$app->user->identity->id;

        return $this->render('statistics', [
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Lists all Activity models.
     * @return mixed
     */
    public function actionReport()
    {
        $params = Yii::$app->request->queryParams;
        $id = !empty($params['id']) ? (int) $params['id'] : null;

        $user = Yii::$app->user->identity;
        if ($user->id != $id && !$user->isAdmin()) {
            return $this->redirect(['statistics']);
        }

        $searchModel = new ActivitySearch();
        $models = $searchModel->getList($params);

        return $this->render('report', [
            'models' => $models,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionCalculations()
    {
        $post = Yii::$app->request->post();
        $userId = !empty($post['userId']) ? (int) $post['userId'] : null;
        $startDate = !empty($post['startDate']) ? strtotime($post['startDate']) : null;
        $endDate = !empty($post['endDate']) ? strtotime($post['endDate']) : null;

        $searchModel = new ActivitySearch();
        $searchModel->user_id = $userId;
        $searchModel->date_from = $startDate;
        $searchModel->date_to = $endDate;
        $models = $searchModel->getList();
        $statistics = $searchModel->getStatistics($models);

        foreach ($statistics as &$statistic) {
            if (isset($statistic['keyboard_activity_percent'])) {
                $statistic['content'] = $this->renderPartial('_cell', ['data' => $statistic, 'userId' => $userId]);
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'success' => true,
            'amount' => count($models),
            'statistics' => $statistics,
        ];
    }

    /**
     * Displays a single Activity model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Activity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Activity();
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Activity model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Change the active attribute in Activity model.
     * @param integer $id
     * @return mixed
     */
    /*public function actionShow($id)
    {
        $model = $this->findModel($id);
        $model->reverseVisible();

        return $this->redirect(['index']);
    }*/

    /**
     * Change the active attribute in Activity models.
     * @param bool $visible
     * @return mixed
     * @throws NotFoundHttpException
     */
    /*public function actionShowList($visible = true)
    {
        $ids = Yii::$app->request->post('ids');
        $success = false;

        if (!empty($ids)) {
            foreach ($ids as $id) {
                $model = $this->findModel($id);
                $success = $model->setVisible($visible);
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'success' => $success,
        ];
    }*/

    /**
     * Deletes an existing Activity model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Deletes existing Activity models.
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDeleteList()
    {
        $ids = Yii::$app->request->post('ids');
        $success = false;

        if (!empty($ids)) {
            foreach ($ids as $id) {
                $model = $this->findModel($id);
                $success = $model->delete();
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'success' => $success,
        ];
    }

    /**
     * Displays a single Activity model.
     * @param integer $id
     * @param boolean $thumbnail
     * @return mixed
     */
    public function actionImageView($id, $thumbnail = false)
    {
        if (!Yii::$app->user->isGuest) {
            $model = $this->findModel($id);

            $mimes = [
                'jpg' => 'image/jpg',
                'jpeg' => 'image/jpg',
                'gif' => 'image/gif',
                'png' => 'image/png'
            ];

            $extension = strtolower(end(explode('.', $model->screenshot)));
            $file = Yii::getAlias('@image.privateStorage') . '/'
                . Yii::$app->storage->getDirName($model) . '/'
                . $model->id . '/'
                . ($thumbnail ? 'thumbnail-' . $model->screenshot : $model->screenshot);

            header('content-type: '. $mimes[$extension]);
            header('content-disposition: inline; filename="' . $model->screenshot . '";');
            readfile($file);
        }
        return $this->redirect(['index']);
    }

    /**
     * Change Activity models sorting.
     * @return mixed
     */
    /*public function actionSort()
    {
        $model = new Activity;

        $sortedIds = Yii::$app->request->post('sortedList');
        $success = !empty($sortedIds) ? $model->updateSorting($sortedIds) : false;

        Yii::$app->response->format = Response::FORMAT_JSON;

        return ['success' => $success];
    }*/

    /**
     * Finds the Activity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Activity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Activity::findOneForUser($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
