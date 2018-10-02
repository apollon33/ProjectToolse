<?php

namespace modules\calendar\controllers\backend;

use common\controllers\BaseController;
use modules\calendar\models\CalendarSearch;
use modules\countingtime\models\CountingTime;
use yii\web\Response;
use modules\project\models\Project;
use modules\holiday\models\Holiday;
use modules\calendar\models\Calendar;
use modules\user_project\models\UserProject;
use modules\vacation\models\Vacation;
use common\models\User;
use common\models\UserSearch;
use yii\web\NotFoundHttpException;
use common\access\AccessManager;
use yii\helpers\Json;
use \yii2fullcalendar\models\Event;
use kartik\mpdf\Pdf;
use yii\grid\GridView;
use common\helpers\Toolbar;
use Yii;


class DefaultController extends BaseController
{
    const NOT_UPDATE = 3;
    const NOT_DELETE_PROFILE = 5;

    public function permissionMapping()
    {
        return [

            'index' => AccessManager::VIEW,
            'list' => AccessManager::VIEW,
            'all-holiday-config' => AccessManager::VIEW,
            'filter-calendar' => AccessManager::VIEW,
            'active-project-user' => AccessManager::VIEW,
            'all-holiday-config' => AccessManager::VIEW,
            'add-report' => AccessManager::UPDATE,
            'create-table' => AccessManager::VIEW,
            'all-report-cart' => AccessManager::VIEW,
            'all-date' => AccessManager::UPDATE,
            'report' => AccessManager::VIEW,
            'reportcart' => AccessManager::VIEW,
            'update' => AccessManager::UPDATE,
            'update-table' => AccessManager::UPDATE,
            'create' => AccessManager::CREATE,
            'delete' => AccessManager::DELETE,
            'view' => AccessManager::VIEW
        ];
    }

    private static function getMessages($options)
    {
        return [

            self::NOT_UPDATE => Yii::t('app', 'You are not update to edit this entry.'),
            self::NOT_DELETE_PROFILE => Yii::t('app', 'Cannot delete or update a parent'),
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        if(!Yii::$app->user->isGuest) {
            $user = User::find()->select('id, first_name, last_name, middle_name')
//                ->where([ 'deleted' => User::DELETED_NO])
                ->orderBy(['last_name' => SORT_ASC])->asArray()->all();
            $project = Project::find()->select('id, name, color')->asArray()->all();
            return $this->render('index', [
                'user' => $user,
                'project' => $project,
            ]);
        } else {
            return $this->redirect(['/']);
        }
    }

    /**
     * Lists all Calendar models.
     *
     * @return mixed
     */
    public function actionList()
    {
        if(!Yii::$app->user->isGuest) {
            $searchModel = new CalendarSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            return $this->render('list', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } else {
            return $this->redirect(['/']);
        }
    }

    /**
     * Creates a new Calendar model.
     *
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        if(!Yii::$app->user->isGuest) {
            $calendar = new Calendar();
            $calendar->scenario = Calendar::SCENARIO_SAVE;
            if ($calendar->load(Yii::$app->request->post()) && $calendar->save()) {
                $event = $calendar->fillEvent($calendar);
                if (Yii::$app->request->isAjax) {
                    return   Json::encode($event);
                }

                return $this->redirect('list');

            } else {
                if (Yii::$app->request->isAjax) {
                    return $this->renderAjax('_form_calendar', [
                        'path' => 'create',
                        'calendar' => $calendar,
                    ]);
                }
                return $this->render('create', [
                    'calendar' => $calendar,
                ]);
            }
        }
        return $this->redirect(['/']);
    }

    public function actionCreateTable()
    {
        $calendar = new Calendar();
        $calendar->scenario = Calendar::SCENARIO_SAVE;
        if ($calendar->load(Yii::$app->request->post()) && $calendar->save()) {
            $event = $calendar->fillEventTable($calendar);
            return   Json::encode($event);
        } else {
            return $this->renderAjax('_form_calendar', [
                'path' => 'create-table',
                'calendar' => $calendar,
            ]);
        }
    }

    /**
     * Updates an existing Calebdar model.
     *
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if(!Yii::$app->user->isGuest) {
            $calendar = $this->findModel($id);
            $calendar->scenario = Calendar::SCENARIO_SAVE;
//            if(Yii::$app->user->identity->role_id === User::TYPE_SUPER_ADMIN) {
                if ($calendar->load(Yii::$app->request->post()) && $calendar->save()) {
                    $event = $calendar->fillEvent($calendar);
                    if (Yii::$app->request->isAjax) {
                        return   Json::encode($event);
                    }
                    return $this->redirect('list');
                } else {
                    if (Yii::$app->request->isAjax) {
                        return $this->renderAjax('_form_calendar', [
                            'path' => $id.'/update',
                            'calendar' => $calendar,
                        ]);
                    }
                    return $this->render('update', [
                        'calendar' => $calendar,
                    ]);
//                }
//            } else {
//                if($calendar->created_by === Yii::$app->user->identity->id) {
//                    if ($calendar->load(Yii::$app->request->post()) && $calendar->save()) {
//                        $event = $calendar->fillEvent($calendar);
//                        if (Yii::$app->request->isAjax) {
//                            return   Json::encode($event);
//                        }
//                        return $this->redirect('list');
//                    } else {
//                        if (Yii::$app->request->isAjax) {
//                            return $this->renderAjax('_form_calendar', [
//                                'path' => $id.'/update',
//                                'calendar' => $calendar,
//                            ]);
//                        }
//                        return $this->render('update', [
//                            'calendar' => $calendar,
//                        ]);
//                    }
//                }
                $this->showMessage(self::NOT_UPDATE, null, 'warning');
                return $this->redirect('list');
            }
        }
        return $this->redirect(['/']);
    }

    public function actionUpdateTable($id)
    {
        $calendar = $this->findModel($id);
        $calendar->scenario = Calendar::SCENARIO_SAVE;
        if ($calendar->load(Yii::$app->request->post()) && $calendar->save()) {
            $event = $calendar->fillEventTable($calendar);
            return   Json::encode($event);
        } else {
            return $this->renderAjax('_form_calendar', [
                'path' => $id.'/update-table',
                'calendar' => $calendar,
            ]);
        }
    }

    /**
     * Lists all Calendar models.
     *
     * @return mixed
     */
    public function actionReport()
    {
        if(!Yii::$app->user->isGuest) {
            $searchModel = new UserSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            return $this->render('report', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }else{
            return $this->redirect(['/']);
        }
    }

    /**
     * Update, insert, delete date to calendar Handsonable
     *
     * @return string
     */
    public function actionAddReport()
    {
        $post = Yii::$app->request->get('changes');
        $startAt = strtotime($post['day'].' '.$post['month'].' '.$post['year'])+9*(60*60);
        $endAt = $startAt+(intval($post['actual_time'])*(60*60));
        if(is_numeric($post['actual_time']) || $post['actual_time']==='') {
            if($post['actual_time'] != '') {
                $calendar = Calendar::findOne($post['id']);
                if(empty($calendar)) {
                    $calendar = new Calendar();
                }
                $calendar->scenario = Calendar::SCENARIO_SAVE_CALENDAR;
                $calendar->user_id = intval($post['user_id']);
                $calendar->project_id = intval($post['id_project']);
                $calendar->start_at = $startAt;
                $calendar->end_at = $endAt;
                $calendar->actual_time = intval($post['actual_time']);
                $calendar->created_by = $post['created_by'];
                if($calendar->save()) {
                    $events = $calendar->fillEventTable($this->findModel($calendar->id));
                    $massage = [
                        'date' => Json::encode($events),
                        'option' => 'update',
                    ];
                    return Json::encode($massage);
                } else{
                    $massage = [
                        'option' => 'error',
                        'type' => 'danger',
                        'text' => Yii::t('app', 'An unexpected error occurred.')
                    ];
                    return Json::encode($massage);
                }
            }else{
                $this->findModel($post['id'])->delete();
                $massage = [
                    'id' => $post['id'],
                    'option' => 'delete',
                ];
                return Json::encode($massage);
            }

        } else{
            $massage = [
                'option' => 'error',
                'type' => 'danger',
                'text' => Yii::t('app', 'Must be a number')
            ];
            return Json::encode($massage);
        }
    }

    /**
     * Output to the Handsonable Calendar
     *
     * @param $id
     *
     * @return string
     */
    public function actionView($id, $year = false, $month = false, $createdBy = false)
    {
        if(!Yii::$app->user->isGuest) {
            $model = new Calendar();
            $model->scenario = Calendar::SCENARIO_SAVE_CALENDAR;
            $calendar =  $model->allCalendar($id, $year, $month, $createdBy);
            $date['project'] = Json::encode($model->allUserProject($calendar, $model->allProjectUser($id)));
            $date['year'] = (!empty($year)  ?  $year : date('Y'));
            $date['month'] = Toolbar::viewMonth((!empty($month)  ?  $month : date('n')));
            $date['created_by'] = (!empty($createdBy)  ?  $createdBy : Yii::$app->user->identity->id);
            $date['model'] = $model;
            return $this->render('view_calendar', $date);
        }else{
            return $this->redirect(['/']);
        }
    }

    /**
     * @param bool $year
     * @param bool $month
     * @param bool $created_by
     * @return string
     */
    public function actionReportcart($id=false, $year=false, $month=false, $createdBy=false)
    {
        if(!Yii::$app->user->isGuest) {
            $model = new Calendar();
            $model->scenario = Calendar::SCENARIO_SAVE_CALENDAR;
            $user_id = $model->allUserReportCart($id, $year, $month, $createdBy);
            $date['id']=$id;
            $date['year'] = (!empty($year) ? $year : date('Y'));
            $date['month'] = Toolbar::viewMonth((!empty($month) ? $month : date('n')));
            $date['created_by'] = (!empty($createdBy) ? $createdBy : Yii::$app->user->identity->id);
            $date['reportcart'] = [];
            foreach ($user_id as $item) {
                $calendar = $model->reportCart($item->user_id,  $year, $month, $createdBy);
                $project = $model->allProjectUser($item->user_id);
                $project = $model->allUserProject($calendar, $project);
                $cdate['project'] = Json::encode($project);
                $cdate['user_id'] = $item->user_id;
                $date['reportcart'][] = $cdate;
            }
            $date['model'] = $model;
            return $this->render('reportcart', $date);
        }else{
            return $this->redirect(['/']);
        }
    }

    public function actionAllReportCart($id=false, $year=false, $month=false, $createdBy=false)
    {
        if(!Yii::$app->user->isGuest) {
            $model = new Calendar();
            $model->scenario = Calendar::SCENARIO_SAVE_CALENDAR;
            $user_id = $model->allUserReportCart($id, $year, $month, $createdBy);
            $date['day'] = Json::encode($model->countDayMonth($month, $year));
            $date['year'] = (!empty($year) ? $year : date('Y'));
            $date['month'] = Toolbar::viewMonth((!empty($month) ? $month : date('n')));
            $date['holiday'] = $model->holidays((!empty($month) ? $month : date('n')));
            $date['created_by'] = (!empty($createdBy) ? $createdBy : Yii::$app->user->identity->id);
            $date['reportcart'] = [];
            foreach ($user_id as $item) {
                $calendar = $model->reportCart($item->user_id, $year, $month, $createdBy);
                $project = $model->allProjectUser($item->user_id);
                $project = $model->allUserProject($calendar, $project);
                $cdate['project'] = Json::encode($project);
                $cdate['calendar'] = Json::encode($calendar);
                $cdate['user_id'] = $item->user_id;
                $vacation = $model->vacationUser(false, $item->user_id, $date['day'], $date['year'], $date['month']);
                $cdate['vacation'] = Json::encode($vacation);
                $date['reportcart'][] = $cdate;
            }
            $weekind = $this->weekendDay($month, $year);
            $date['weekind'] = Json::encode($weekind);
            $date['id'] = $id;
            return Json::encode($date);
        }
    }

    public function actionAllDate($id, $year=false, $month=false, $createdBy=false)
    {
        if(!Yii::$app->user->isGuest) {
            $model = new Calendar();
            $model->scenario = Calendar::SCENARIO_SAVE_CALENDAR;
            $calendar =  $model->allCalendar($id, $year, $month, $createdBy);
            $project = $model->allProjectUser($id);
            $date['project'] = Json::encode($model->allUserProject($calendar, $project));
            $date['calendar'] = Json::encode($calendar);
            $date['day'] = Json::encode($model->countDayMonth($month, $year));
            $date['year'] = (!empty($year)  ?  $year : date('Y'));
            $date['month'] = Toolbar::viewMonth((!empty($month)  ?  $month : date('n')));
            $date['holiday'] = $model->holidays((!empty($month)  ?  $month : date('n')));
            $date['created_by'] = (!empty($createdBy)  ?  $createdBy : Yii::$app->user->identity->id);
            $weekind = $this->weekendDay($month, $year);
            $vacation = $model->vacationUser(false, $id, $date['day'], $date['year'], $date['month']);
            $date['weekind'] = Json::encode($weekind);
            $date['vacation'] = Json::encode($vacation);
            $date['id'] = $id;
            return Json::encode($date);
        }
    }

    /**
     * Deletes an existing Calendar model.
     * If deletion is successful, the browser will be redirected to the 'list' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        if(!Yii::$app->user->isGuest) {
            $calendar = $this->findModel($id);
            $calendar->scenario = Calendar::SCENARIO_SAVE;
            if(Yii::$app->user->identity->isAllowedToViewStage()) {
                $this->findModel($id)->delete();
                if (Yii::$app->request->isAjax) {
                    return 'complete';
                } else {
                    return $this->redirect(['list']);
                }
            }else{
                if($calendar->created_by === Yii::$app->user->identity->id) {
                    $this->findModel($id)->delete();
                    if (Yii::$app->request->isAjax) {
                        return 'complete';
                    } else {
                        return $this->redirect(['list']);
                    }
                }
                $this->showMessage(self::NOT_UPDATE, null, 'warning');
                return $this->redirect('list');
            }
        }else{
            return $this->redirect(['/']);
        }
    }

    /**
     * @return string
     */
    public function actionFilterCalendar()
    {
        $post = Yii::$app->request->post();
        $calendar = new Calendar();
        $events = $calendar->filterCalendar($post);
        return  Json::encode($events);

    }

    /**
     * @return array
     *
     */
    public function actionAllHolidayConfig()
    {
        if(!Yii::$app->user->isGuest) {
            $holiday = Holiday::find()->asArray()->all();
            return Json::encode($holiday);
        }
    }

/*    public function actionEditDropCalendar()
    {
        $post=Yii::$app->request->post('project');
        $calendar = Calendar::findOne($post['id']);
        $calendar->scenario = Calendar::SCENARIO_SAVE;
        $calendar->start_at = strtotime($post['start']);
        $calendar->end_at = strtotime($post['end']);
        $calendar->save();
    }*/

    /**
     * @return array
     */
    public function actionActiveProjectUser()
    {
        $post=Yii::$app->request->post();
        $project=new Calendar();
        return Json::encode($project->activeProjectUser((int) $post['user_id']));
    }

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
     * Finds the calendar model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Calendar the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Calendar::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    private function showMessage($errorId, $options = [], $type = 'error')
    {
        Yii::$app->getSession()->setFlash($type, [self::getMessages($options)[$errorId]]);
    }

    public function weekendDay($month, $year)
    {
        $i = 1;
        $day = cal_days_in_month(CAL_GREGORIAN, (!empty($month) ? $month : date('n')), (!empty($year) ? $year : date('Y')));
        $start = strtotime(Toolbar::viewMonth((!empty($month)  ?  $month : date('n'))).' '.(!empty($year)  ?  $year : date('Y')));
        while($day >= $i){
            if(date('w', $start) == 6 || date('w', $start) == 0) {
                $weekind[] = $i;
            }
            $start+=(24*60*60);
            $i++;
        }
        return $weekind;
    }


}