<?php

namespace modules\vacation\controllers\backend;

use common\access\AccessManager;
use hbattat\VerifyEmail;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\{NotFoundHttpException, ForbiddenHttpException};
use yii\web\Response;
use modules\vacation\models\Vacation;
use modules\vacation\models\VacationSearch;
use common\controllers\BaseController;
use common\models\User;
use modules\calendar\models\Calendar;
use common\helpers\Toolbar;
use yii\helpers\Json;
use Carbon\Carbon;
use modules\holiday\models\Holiday;
use yii\web\HttpException;

/**
 * DefaultController implements the CRUD actions for Vacation model.
 */
class DefaultController extends BaseController
{

    public function permissionMapping()
    {
        return [
            'index' => AccessManager::VIEW,
            'table' => AccessManager::UPDATE,
            'calendar' => AccessManager::VIEW,
            'withdrawal-event' => AccessManager::VIEW,
            'generalvacation' =>   AccessManager::VIEW,
            'filter-vacation' => AccessManager::VIEW,
            'all-general-vacation' => AccessManager::VIEW,
            'filter-vacation' => AccessManager::VIEW,
            'update' => AccessManager::UPDATE,
            'update-table' => AccessManager::UPDATE,
            'create-cart-user' => AccessManager::UPDATE,
            'create' => AccessManager::CREATE,
            'create-table' => AccessManager::CREATE,
            'delete' => AccessManager::DELETE,
            'delete-list' => AccessManager::DELETE,
            'update-user-vacation' => AccessManager::UPDATE,
            'delete-user-vacation' => AccessManager::UPDATE,
        ];
    }

    public function actionTable()
    {
        $searchModel = new VacationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Lists all Vacation models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest) {
            $user = User::find()->select('id , first_name , last_name, middle_name')->asArray()->all();
            $vacation = new Vacation();

            return $this->render('calendar', [
                'user' => $user,
                'vacation' => $vacation,
            ]);
        } else {
            return $this->redirect(['/']);
        }
    }

    /**
     * Creates a new Vacation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return string
     * @throws \Throwable
     */
    public function actionCreateCartUser($id = null)
    {
        $model = new Vacation();
        $model->scenario = Vacation::SCENARIO_CART_USER;
        $model->loadDefaultValues();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $modelUser = $model->user;
            $leftVacation = Vacation::leftVacation($modelUser);
            $fullVacation = Vacation::experienceUser($modelUser);
            return $this->renderAjax('@backend/views/user/_vacation_form',
                [
                    'userId' => $modelUser->id,
                    'leftVacation' => $leftVacation,
                    'listVacation' => $modelUser->vacation,
                    'fullVacation' => $fullVacation,
                    'allVacationList' => $modelUser->vacations

                ]);
        } else {
            return $this->renderAjax('_form_vacation',
                [
                    'userId' => $id,
                    'path' => 'create-cart-user',
                    'vacation' => $model,
                ]);
        }
    }


    /**
     * Creates a new Vacation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Vacation();
        $model->scenario = Vacation::SCENARIO_CART_USER;
        $model->loadDefaultValues();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $event = $model->modelEventAttribut();
            if (Yii::$app->request->isAjax) {
                return Json::encode($event);
            } else {
                return $this->redirect(['table']);
            }
        } else {
            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('_form_vacation',
                    [
                        'path' => 'create',
                        'vacation' => $model,
                    ]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Updates an existing Vacation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $user = $model->user;
        $model->scenario = Vacation::SCENARIO_CART_USER;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $event = $model->modelEventAttribut();
            if (Yii::$app->request->isAjax) {
                return Json::encode($event);
            } else {
                return $this->redirect(['table']);
            }
        } else {
            $authors = User::find()->all();
            $items = ArrayHelper::map($authors, 'id', 'first_name');
            if (Yii::$app->request->isPjax) {
                return $this->renderAjax('_form_vacation',
                    [
                        'path' => $id . '/update',
                        'vacation' => $model,
                        'userId' => $user->id,
                    ]);
            } else {
                return $this->render('create', [
                    'items' => $items,
                    'model' => $model,
                ]);
            }

        }
    }

    /**
     * @param bool $month
     * @param bool $year
     *
     * @return string
     */
    public function actionGeneralvacation($month = false, $year = false)
    {
        $vacation = new Vacation();
        $year = (!empty($year) ? $year : date('Y'));
        $month = Toolbar::viewMonth((!empty($month) ? $month : date('n')));

        return $this->render('general_vacation', [
            'model' => $vacation,
            'year' => $year,
            'month' => $month,
        ]);
    }

    /**
     * @param bool $month
     * @param bool $year
     *
     * @return string
     */
    public function actionAllGeneralVacation($month = false, $year = false)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $calendar = new Calendar();
        $vacation = new Vacation();
        $year = (!empty($year) ? $year : date('Y'));
        $day = cal_days_in_month(CAL_GREGORIAN, (!empty($month) ? $month : date('n')), $year);
        $users = User::getList();
        $modelUser = new User();
        $experience = $modelUser->experienceUser();
        $leftVacation = $vacation->remainingVacationDays(User::find()->all(), $year);
        $date = Carbon::create(empty($year) ? date('Y') : intval($year), empty($month) ? date('F') : intval($month), 1, 0);
        $vacation->year = $year;
        $monthVacation = $vacation->listVacation($date->timestamp, $date->copy()->addMonth()->timestamp - 1);
        $date = Carbon::create(empty($year) ? date('Y') : intval($year), 1, 1, 0);
        $yearVacation = $vacation->listVacation($date->timestamp,  $date->copy()->endOfYear()->timestamp - 1);
        $headerTable = [
            Yii::t('app', 'Full Name'),
            Yii::t('app', 'Experience'),
            Yii::t('app', 'Vacation left'),
            Yii::t('app', 'Paid leave'),
            Yii::t('app', 'Not Paid'),
            Yii::t('app', 'Sick Leave'),
            Yii::t('app', 'All'),
        ];
        $holiday = $vacation->holidays((!empty($month) ? $month : date('n')));
        $weekind = $vacation->weekendDay($month, $year);
        $month = Toolbar::viewMonth((!empty($month) ? $month : date('n')));
        $typeVacation = Vacation::getCurrency();

        return [
            'headerTable' => $headerTable,
            'vacation' => $monthVacation,
            'yearVacation' => $yearVacation,
            'experience' => $experience,
            'leftVacation' => $leftVacation,
            'typeVacation' => $typeVacation,
            'titleWeekind' => Yii::t('app', 'Weekend'),
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'weekind' => $weekind,
            'holiday' => $holiday,
            'users' => $users,
        ];
    }

    /**
     * @return string
     */
    public function actionViewEvent()
    {
        $post = Yii::$app->request->post();
        $vacation = new Vacation();
        $name = User::getList();
        $evant = $this->findModel((int)$post['id']);

        return Json::encode($Event = [
            'id' => $evant->id,
            'user_id' => $evant->user_id,
            'type' => $evant->type,
            'title' => Yii::t('app', 'Vacation') . ' - ' . $name[$evant->user_id],
            'start' => date('Y-m-d', $evant->start_at),
            'end' => date('Y-m-d', $evant->end_at),
            'days' => $vacation->countVacationDaysTariff($evant->start_at, $evant->end_at),
            'description' => $evant->description,
        ]);
    }

    public function actionFilterVacation()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $post = Yii::$app->request->post();
        $vacation = new Vacation();
        $events = $vacation->filterVacation($post);

        return $events;

    }

    public function actionModalWindow()
    {
        $vacation = new Vacation();

        $model = $this->render('_form_vacation', [
            'vacation' => $vacation,
        ]);

        return $model;
    }


    /**
     * Deletes an existing Vacation model and sends a removal notification to the person in charge
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws  \Throwable
     */
    public function actionDelete($id)
    {
        $vacation = Vacation::findOne($id);
        $delete = $vacation->delete();
        if (!$delete) {
            throw new HttpException(422, 'Delete error');
        }
        if (Yii::$app->request->isAjax) {
            return 'complete';
        } else {
            return $this->redirect(['table']);
        }
    }

    /**
     * Deletes existing Vacation models.
     *
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


    public function actionWithdrawalEvent($start = null, $end = null, $_ = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $vacation = new Vacation();

        return $vacation->listEventVacation();
    }


    /**
     * Finds the Vacation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Vacation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Vacation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    /**
     * Update user information on vacations
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUpdateUserVacation($id)
    {
        $vacation = $this->findModel($id);
        $user = $vacation->user;
        $vacation->scenario = Vacation::SCENARIO_CART_USER;
        if ($vacation->load(Yii::$app->request->post()) && $vacation->save()) {
            $leftVacation = Vacation::leftVacation($user);
            $fullVacation = Vacation::experienceUser($user);
            return $this->renderAjax('@backend/views/user/_vacation_form',
                [
                    'userId' => $user->id,
                    'leftVacation' => $leftVacation,
                    'listVacation' => $user->vacation,
                    'allVacationList' => $user->vacations,
                    'fullVacation' => $fullVacation
                ]);
        } else {
            return $this->renderAjax('_form_vacation',
                [
                    'vacation' => $vacation,
                    'userId' => $user->id,
                ]);
        }
    }

    /**
     * Delete a vacation by vacation id, using pjax 
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionDeleteUserVacation($id)
    {
        $vacation = Vacation::findOne($id);
        $user = $vacation->user;
        $delete = $vacation->delete();
        if (!$delete) {
            throw new \HttpException('Delete error');
        }
        $leftVacation = Vacation::leftVacation($user);
        $fullVacation = Vacation::experienceUser($user);
        return $this->renderAjax('@backend/views/user/_vacation_form',
            [
                'userId' => $user->id,
                'leftVacation' => $leftVacation,
                'listVacation' => $user->vacation,
                'allVacationList' => $user->vacations,
                'fullVacation' => $fullVacation
            ]);
    }
}
