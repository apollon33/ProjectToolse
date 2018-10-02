<?php

namespace modules\registration\controllers\backend;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use modules\registration\models\Registration;
use modules\registration\models\RegistrationSearch;
use common\controllers\BaseController;

use common\access\AccessManager;

/**
 * DefaultController implements the CRUD actions for Registration model.
 */
class DefaultController extends BaseController
{

    public function permissionMapping()
    {
        return [
            'index' => AccessManager::VIEW,
            'update' => AccessManager::UPDATE,
            'create' => AccessManager::CREATE,
            'delete' => AccessManager::DELETE,
        ];
    }
    /**
     * Lists all Registration models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RegistrationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Registration model.
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
     * Creates a new Registration model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Registration();
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
     * Updates an existing Registration model.
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


    public function actionUpdatePosition()
    {
        $post = Yii::$app->request->post();

        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!empty($post['hasEditable'])) {
            $key = json_decode($post['editableKey']);
            $attribute = $post['editableAttribute'];
            $index = $post['editableIndex'];
            $value = $post['Registration'][$index][$attribute];
            $model = $this->findModel($key);
            $model->$attribute = $value;
            if ($model->save()) {
                return [
                    'output' => $value,
                ];
            }
        }

        return [
            'message' => Yii::t('app', 'Saving value error.'),
        ];
    }

    /**
     * Change the active attribute in Registration model.
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
     * Change the active attribute in Registration models.
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
     * Deletes an existing Registration model.
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
     * Deletes existing Registration models.
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
     * Change Registration models sorting.
     * @return mixed
     */
    /*public function actionSort()
    {
        $model = new Registration;

        $sortedIds = Yii::$app->request->post('sortedList');
        $success = !empty($sortedIds) ? $model->updateSorting($sortedIds) : false;

        Yii::$app->response->format = Response::FORMAT_JSON;

        return ['success' => $success];
    }*/

    /**
     * Finds the Registration model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Registration the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Registration::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
