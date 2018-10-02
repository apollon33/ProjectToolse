<?php

namespace modules\client\controllers\backend;


use common\controllers\BaseController;
use modules\client\models\ContactPerson;
use modules\client\models\Counterparty;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use modules\client\models\CounterpartySearch;
use common\access\AccessManager;


class CounterpartyController extends BaseController
{
    /**
     * @return array
     */
    public function permissionMapping()
    {
        return [
            'index' => AccessManager::VIEW,
            'view' => AccessManager::UPDATE,
            'update' => AccessManager::UPDATE,
            'create' => AccessManager::CREATE,
            'delete' => AccessManager::DELETE,
            'contacts' => AccessManager::UPDATE,
            'updateContacts' => AccessManager::UPDATE,
            'deleteContacts' => AccessManager::DELETE,
        ];
    }
    /**
     * Lists all Counterparty models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CounterpartySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Counterparty model.
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
     * Creates a new Counterparty model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new Counterparty();
        $model->loadDefaultValues();
        if (Yii::$app->request->post()) {
            $model->load(Yii::$app->request->post());
            $model->client_id = $id;
            if ($model->save()) {
                return $this->redirect(['/client/view', 'id' => $id]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Counterparty model.
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
     * Change the active attribute in Counterparty model.
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
     * Change the active attribute in Counterparty models.
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
     * Deletes an existing Counterparty model.
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
     * Deletes existing Counterparty models.
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
     * Change Counterparty models sorting.
     * @return mixed
     */
    /*public function actionSort()
    {
        $model = new Counterparty;

        $sortedIds = Yii::$app->request->post('sortedList');
        $success = !empty($sortedIds) ? $model->updateSorting($sortedIds) : false;

        Yii::$app->response->format = Response::FORMAT_JSON;

        return ['success' => $success];
    }*/

    /**
     * Finds the Counterparty model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Counterparty the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Counterparty::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    /**
     * @param $id
     * @return string|Response
     */
    public function actionContacts($id)
    {
        $contact = new ContactPerson();
        if (Yii::$app->request->post()) {
            $contact->load(Yii::$app->request->post());
            $contact->company_id = $id;
            if ($contact->save()) {
                return $this->redirect(['counterparty/view', 'id' => $id]);
            }
        }
        return $this->render('contact', [
            'contact' => $contact,
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     */
    public function actionUpdateContacts($id)
    {
        $contact = ContactPerson::findOne($id);
        if ($contact->load(Yii::$app->request->post()) && $contact->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update_contact', [
                'contact' => $contact,
            ]);
        }

    }

    /**
     * @param $id
     * @return Response
     */
    public function actionDeleteContacts($id)
    {
        $contact = ContactPerson::findOne($id);
        $contact->delete();
        return $this->redirect(['index']);
    }
}
