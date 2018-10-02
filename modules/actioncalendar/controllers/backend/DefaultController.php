<?php

namespace modules\actioncalendar\controllers\backend;

use common\controllers\BaseController;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use modules\actioncalendar\models\Event;
use modules\holiday\models\HolidaySearch;
use modules\actioncalendar\models\EventSearch;

use common\access\AccessManager;

/**
 * DefaultController implements the CRUD actions for Event model.
 */
class DefaultController extends BaseController
{

    public function permissionMapping()
    {
        return [
            'index' => AccessManager::VIEW,
            'event' => AccessManager::UPDATE,
            'view' => AccessManager::VIEW,
            'withdrawal-event' => AccessManager::VIEW,
            'update' => AccessManager::UPDATE,
            'create' => AccessManager::CREATE,
            'delete' => AccessManager::DELETE,
        ];
    }


    public function actionIndex()
    {
        $model = new Event();
        $model->loadDefaultValues();

        $row = 7;//row holiday filter

        $searchModel = new HolidaySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize=$row;

        return $this->render('_calendar', [
                'model' => $model,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Lists all Event models.
     * @return mixed
     */
    public function actionEvent()
    {
//        if(Yii::$app->user->identity->isAdmin()) {
            $searchModel = new EventSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
//        }

//        return $this->redirect(['index']);

    }

    public function actionWithdrawalEvent($start=null, $end=null, $_=null)
    {

        Yii::$app->response->format = Response::FORMAT_JSON;

        $times=Event::find()->where([
            'active' => Event::ACTIVE_NO
        ])->all();

        $events = array();

        foreach ($times AS $time){
            $Event = new \yii2fullcalendar\models\Event();
            $Event->id = $time->id;
            $Event->title = $time->name;
            $Event->className = 'actionCelendar';
            $Event->start = date('Y-m-d\TH:i:s\Z',$time->start_at);
            $Event->end = date('Y-m-d\TH:i:s\Z',$time->end_at);
            $events[] = $Event;
        }

        return $events;
    }

    /**
     * Displays a single Event model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if(Yii::$app->request->isPjax){
            return $this->renderAjax('view', [
                'model' => $this->findModel($id),
            ]);
        } else {
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }

    }

    /**
     * Creates a new Event model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Event();
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (Yii::$app->request->isPjax) {

                $model = new Event();

                return $this->renderAjax('_form_event', [
                    'model' => $model,
                    ]);
            } else {
                return $this->redirect(['event']);
            }
        }

        return $this->render('create', [
                'model' => $model,
            ]);
    }

    /**
     * Updates an existing Event model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
//        if(Yii::$app->user->identity->isAdmin()) {

            $model = $this->findModel($id);

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['index']);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
//        }
//
//        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing Event model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
//        if(Yii::$app->user->identity->isAdmin()) {
            $this->findModel($id)->delete();
//        }
//        return $this->redirect(['index']);

    }

    /**
     * Deletes existing Event models.
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDeleteList()
    {
//        if(Yii::$app->user->identity->isAdmin()) {
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
//        }
//
//        return $this->redirect(['index']);
    }


    /**
     * Finds the Event model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Event the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Event::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
