<?php

namespace modules\position\controllers\backend;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use modules\position\models\Position;
use modules\position\models\PositionSearch;
use common\controllers\BaseController;

use common\access\AccessManager;

/**
 * DefaultController implements the CRUD actions for Position model.
 */
class DefaultController extends BaseController
{

    public function permissionMapping()
    {
        return [
            'index' => AccessManager::UPDATE,
            'update' => AccessManager::UPDATE,
            'create' => AccessManager::CREATE,
            'sort' => AccessManager::VIEW,
            'delete' => AccessManager::DELETE,
            'delete-list' => AccessManager::DELETE,
        ];
    }


    /**
     * Lists all Position models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PositionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Position model.
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
     * Creates a new Position model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Position();
        $model->loadDefaultValues();
        if (strpos(Yii::$app->request->referrer, 'structure') !== false) {
            $referrer = Yii::$app->request->referrer;
        } else {
            $referrer = null;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (!empty(Yii::$app->request->post('referrer'))) {
                $referrer = explode('?',Yii::$app->request->post('referrer'));
                return $this->redirect([$referrer[0].'?id=position-'.$model->id]);
            }

            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'referrer' => $referrer,
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
            $value = $post['Position'][$index][$attribute];
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
     * Updates an existing Position model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (strpos(Yii::$app->request->referrer, 'structure') !== false) {
                $referrer = explode('?',Yii::$app->request->referrer);
                return $this->goBack((!empty($referrer[0]) ? $referrer[0].'?id=position-'.$id : null));
            }

            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Change the active attribute in Position model.
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
     * Change the active attribute in Position models.
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
     * Deletes an existing Position model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        if (strpos(Yii::$app->request->referrer, 'structure') !== false) {
            return $this->goBack((!empty(Yii::$app->request->referrer) ? Yii::$app->request->referrer : null));
        }

        return $this->redirect(['index']);
    }

    /**
     * Deletes existing Position models.
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
     * Change Position models sorting.
     * @return mixed
     */
    public function actionSort()
    {
        $model = new Position;

        $sortedIds = Yii::$app->request->post('sortedList');
        $success = !empty($sortedIds) ? $model->updateSorting($sortedIds) : false;

        Yii::$app->response->format = Response::FORMAT_JSON;

        return ['success' => $success];
    }

    /**
     * Finds the Position model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Position the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Position::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
