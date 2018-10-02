<?php

namespace modules\project\controllers\backend;

use common\access\AccessManager;
use common\controllers\BaseController;
use Yii;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use modules\project\models\Project;
use modules\project\models\ProjectSearch;
use modules\user_project\models\UserProject;

/**
 * DefaultController implements the CRUD actions for Project model.
 */
class DefaultController extends BaseController
{

    public function permissionMapping()
    {
        return [
            'index' => AccessManager::VIEW,
            'assignment' => AccessManager::VIEW,
            'update' => AccessManager::UPDATE,
            'create' => AccessManager::CREATE,
            'delete' => AccessManager::DELETE,
        ];
    }

    /**
     * Lists all Project models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProjectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Project model.
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
     * Creates a new Project model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Project();
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $project = Yii::$app->request->post('Project');
            $model->saveUserProject($project['users'], $model->id);
            if(Yii::$app->request->isAjax) {
                return 'complete';
            } else {
                return $this->redirect(['index']);
            }
        } else {
            if(Yii::$app->request->isAjax) {
                return $this->renderAjax('_form', [
                    'model' => $model,
                ]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Updates an existing Project model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $project = Yii::$app->request->post('Project');
            $model->saveUserProject($project['users'], $model->id);
            if(Yii::$app->request->isAjax) {
                return 'complete';
            } else {
                return $this->redirect(['index']);
            }

        } else {
            $model->users=$model->viewUserProject($id);
            if(Yii::$app->request->isAjax) {
                return $this->renderAjax('_form', [
                    'model' => $model,
                ]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }



    public function actionAssignment($id)
    {
        $model = new Project();
        $post=Yii::$app->request->post('Project');
        if(!empty($post)) {
            $model->assignmentUser($post['name'], $id);
            return 'complete';
        } else {
            $model->name=$model->viewProjectAssignment($id);
            return $this->renderAjax('_form_assignment', [
                'model' => $model,
            ]);

        }

    }

    /**
     * Change the active attribute iProject model.
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
     * Change the active attribute in Project models.
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
     * Deletes an existing Project model.
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
     * Deletes existing Project models.
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
     * Change Project models sorting.
     * @return mixed
     */
    /*public function actionSort()
    {
        $model = new Project;

        $sortedIds = Yii::$app->request->post('sortedList');
        $success = !empty($sortedIds) ? $model->updateSorting($sortedIds) : false;

        Yii::$app->response->format = Response::FORMAT_JSON;

        return ['success' => $success];
    }*/

    /**
     * Finds the Project model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Project the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Project::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
