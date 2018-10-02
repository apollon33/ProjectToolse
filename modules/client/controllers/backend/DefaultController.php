<?php

namespace modules\client\controllers\backend;

use modules\client\models\Counterparty;
use Yii;
use yii\base\Exception;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use modules\client\models\Client;
use modules\document\models\Document;
use modules\client\models\ClientSearch;
use common\controllers\BaseController;
use common\access\AccessManager;

/**
 * DefaultController implements the CRUD actions for Client model.
 */
class DefaultController extends BaseController
{
    public function permissionMapping()
    {
        return [
            'index' => AccessManager::VIEW,
            'view' => AccessManager::UPDATE,
            'update' => AccessManager::UPDATE,
            'create' => AccessManager::CREATE,
            'delete' => 'client.backend.admin:' . AccessManager::DELETE,
            'delete-list' => 'client.backend.admin:' . AccessManager::DELETE,
            'archive' => AccessManager::DELETE,
            'archive-list' => AccessManager::DELETE,
            'restore-list' => 'client.backend.admin:' . AccessManager::UPDATE,
            'restore' => 'client.backend.admin:' . AccessManager::UPDATE
        ];
    }

    /**
     * Lists all Client models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * @return string
     */
    public  function actionTrash()
    {

        $searchModel = new ClientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Client::DELETED_YES);

        return $this->render('trash', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * @param $id
     * @return string
     */
    public function actionArchive($id)
    {

        $model = $this->findModel($id);
        $model->archive();

        $this->redirect('index');

    }


    /**
     * @return string
     */
    public function actionArchiveList()
    {

        $ids = Yii::$app->request->post('ids');

        if (!empty($ids)) {
            foreach ($ids as $id) {
                $model = $this->findModel($id);
                $model->archive();
            }
        }

        $this->redirect('index');
    }

    /**
     * Displays a single Client model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $client = $this->findModel($id);
        if (!$client->deleted) {
            return $this->render('view', [
                'client' => $client,
            ]);
        }

        return $this->redirect(['index']);
    }

    /**
     * Creates a new Client model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $client = new Client();
        $client->loadDefaultValues();
        $model_counterparty = new Counterparty();
        if ($client->load(Yii::$app->request->post()) && $client->save()) {
            $model_counterparty->client_id = $client->id;
            if ($model_counterparty->load(Yii::$app->request->post()) && $model_counterparty->save(false)) {
                return $this->redirect(['index']);
            }
        } else {
            return $this->render('create', [
                'counterparties' => $model_counterparty,
                'client' => $client,
            ]);
        }
    }

    /**
     * Updates an existing Client model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $client = $this->findModel($id);
        if (!(boolean)$client->deleted) {
            if ($client->load(Yii::$app->request->post()) && $client->save()) {
                return $this->redirect(['index']);
            }
            return $this->render('update', [
                'client' => $client,
            ]);
        }
        return $this->redirect(['index']);

    }

    /**
     * Deletes an existing Client model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        return $this->redirect(['trash']);
    }


    /**
     * @return Response
     */
    public function actionRestore($id)
    {

        $model = $this->findModel($id);
        $model->restore();

        return $this->redirect('trash');
    }


    /**
     * @return Response
     */
    public function actionRestoreList()
    {
        $ids = Yii::$app->request->post('ids');

        if (!empty($ids)) {
            foreach ($ids as $id) {
                $model = $this->findModel($id);
                $model->restore();
            }
        }

        return $this->redirect('trash');
    }

    /**
     * Deletes existing Client models.
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDeleteList()
    {
        $ids = Yii::$app->request->post('ids');

        if (!empty($ids)) {
            foreach ($ids as $id) {
                $this->findModel($id)->delete();

                return $this->redirect(['trash']);
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->redirect(['trash']);
    }

    protected function findModel($id)
    {
        if (($model = Client::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

}
