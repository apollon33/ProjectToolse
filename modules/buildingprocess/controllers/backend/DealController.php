<?php

namespace modules\buildingprocess\controllers\backend;

use common\access\AccessInterface;
use modules\buildingprocess\models\ProcessInstance;
use modules\buildingprocess\models\ProcessInstanceSearch;
use modules\document\models\Permission;
use modules\field\models\ProcessFieldInstance;
use modules\field\models\ProcessFieldTemplate;
use Yii;
use common\access\AccessManager;

use common\access\AccessRoleApplicant;
use yii\filters\AccessControl;
use yii\httpclient\JsonFormatter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use yii\helpers\Json;

use modules\buildingprocess\models\ProcessTemplate;

use common\controllers\BaseController;

use modules\buildingprocess\models\Discussion;
use modules\document\models\Document;
use common\models\User;
use yii\helpers\ArrayHelper;

/**
 * DefaultController implements the CRUD actions for BuildingProcess model.
 */
class DealController extends BaseController
{
    const MESSAGE_STAGE_NOT = 2;
    const MESSAGE_FINAL_STAGE = 3;
    const NOT_PERMISSION = 4;
    const ERROR_PERMISSION = 5;
    const HTTP_STAT_BAD_REQUEST = 400;


    /**
     * @param $options
     *
     * @return array
     */
    private static function getMessages($options)
    {
        return [
            self::NOT_PERMISSION => Yii::t('app', 'Permission restricted'),
            self::ERROR_PERMISSION => Yii::t('app', 'An unexpected error occurred.'),
            self::MESSAGE_FINAL_STAGE => Yii::t('app', 'Congrats! You have reached the deal closure!'),
            self::MESSAGE_STAGE_NOT => Yii::t('app', 'Stages have not been appointed'),
        ];
    }

    public function permissionMapping()
    {
        return [
            'index' => AccessManager::VIEW,
            'update' => AccessManager::UPDATE,
            'view' => AccessManager::UPDATE,
            'create' => AccessManager::CREATE,
            'client' => AccessManager::UPDATE,
            'deal-permission' => AccessManager::UPDATE,
            'filling-stages' => AccessManager::CREATE,
            'delete' => AccessManager::DELETE,
            'view-process' => AccessManager::CREATE,
            'delete-list' => AccessManager::DELETE,
            'tabs' => AccessManager::CREATE,
            'discussion-update' => AccessManager::UPDATE,
            'delete-discussion-message' => AccessManager::DELETE,
            'discussion-document-create' => AccessManager::UPDATE,
            'document' => 'document.backend.default:' . AccessManager::VIEW,
            'document-preview' => AccessManager::CREATE
        ];
    }

    /**
     * Lists all ProcessInstance models.
     *
     * @param integer $type
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProcessInstanceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('_table_deal', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return bool
     */
    public function actionClient()
    {
        if (!empty(Yii::$app->request->post('client_id'))) {
            $client_id = Yii::$app->request->post('client_id');
            $process_id = Yii::$app->request->post('process_id');
            $process_instance = ProcessInstance::getProcessId($process_id);
            $process_field_template = ProcessFieldTemplate::getProcess($process_instance->process_id);
            $client = ProcessFieldInstance::getData($process_id, $process_field_template->id);
            $client->data = $client_id;
            return $client->save();
        }
    }

    /**
     * Creates a new ProcessInstance model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate($type = null)
    {
        $processInstance = new ProcessInstance();
        $modelPermission = new Permission();
        $processInstance->loadDefaultValues();
        if ($processInstance->load(Yii::$app->request->post()) && $processInstance->save()) {
            $processInstance->assignPermission(AccessManager::VIEW);
            $processInstance->applicant = new AccessRoleApplicant(User::ROLE_SALES_MANAGER);
            $processInstance->assignPermission(AccessManager::VIEW);
            $processInstance->applicant = new AccessRoleApplicant(User::ROLE_PROJECT_MANAGER);
            $processInstance->assignPermission(AccessManager::VIEW);

            $post = Yii::$app->request->post();
            $processField = $processInstance->processField($post['Field']);
            $formStageChildren = $this->renderAjax('_form_deal_update', [
                'path' => ['view', 'id' => $processInstance->id, 'type' => $type],
                'processField' => $processField,
                'processInstance' => $processInstance,
            ]);

            return $this->renderAjax('_form_create', [
                'formStageChildren' => $formStageChildren,
                'processInstance' => $processInstance,
            ]);
        }

        return $this->render('create', [
            'modelPermission' => $modelPermission,
            'processInstance' => $processInstance,
        ]);
    }

    /**
     * @param integer $id
     * @param string $type
     *
     * @return Response index
     */
    public function actionDealPermission($type, $id)
    {
        $processInstance = new ProcessInstance();
        if(!empty($id)) {
            $processInstance = $this->findModel($id);
        }
        $permission = Yii::$app->request->post('Permission');

        if ($processInstance->load(Yii::$app->request->post()) && $processInstance->save()) {
            $processInstance->appointmentPermissionToStage($permission);
            if(!$processInstance->isPermissinDealSuperAdmin()) {
                $processInstancePrev = $processInstance->prev;
                if (!empty($processInstancePrev)) {
                    $processInstancePrev->assignPermission(AccessManager::ASSIGN);

                }
            }

        }


        return $this->redirect([
            'view',
            'id' => $processInstance->parent,
            'type' => $type,
            'tab' => $processInstance->id,
        ]);
    }

    /**
     * @param integer $id
     *
     * @return array
     */
    public function actionViewProcess($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $processInstance = new ProcessInstance();
        $buildingProcess = $this->findModelProcessTemplate($id);

        $processField = $buildingProcess->processField($processInstance);

        $form = $this->renderAjax('_form_deal_update', [
            'processField' => $processField['processField'],
            'processInstance' => $processInstance,
        ]);

        return [
            'view' => $form,
            'validation' => $processField['validationFormStage'],
        ];


    }

    /**
     * @param $id
     */
    protected function findModelProcessTemplate($id)
    {
        return ProcessTemplate::findOne($id);
    }

    /**
     * Updates an existing ProcessInstance model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @param integer $tab
     * @param string $type
     *
     * @return mixed
     */
    public function actionView($id, $type = null, $tab = null)
    {
        $processInstance = $this->findModel($id);
        $discussionMessagesId = Discussion::getDiscussionMessagesIdByProcess($processInstance);
        $messages = [];
        if ($discussionMessagesId) {
            $document = Document::findOne($discussionMessagesId);
            if (!empty($document)) {
                $messages = Discussion::getSortedArrayMessages($document) ?? $document;
            }
        }
        $index = 0;
        $action = 0;
        foreach ($processInstance->descendants as $key => $instance) {
            if ($instance->id === (int)$tab) {
                $index = $key;
            }

            $action = $key;
        }

        if(!empty($processInstance->descendants)) {
            $processInstanceId = !empty($tab) ? $index : $action;
            if ($processInstanceId > 0) {
                $previousProcessInstanceId = $processInstanceId - 1;
                $previousFirstProcessInstance = $processInstance->descendants[$previousProcessInstanceId];
                $previousPermission = current($previousFirstProcessInstance->getListAllAccessHolders(AccessManager::ASSIGN));
            }

            $firstProcessInstance = $processInstance->descendants[$processInstanceId];
            $buildingProcess = $this->findModelProcessTemplate($firstProcessInstance->process_id);
            $processField = $buildingProcess->processField($firstProcessInstance);
            $permission = current($firstProcessInstance->getListAllAccessHolders(AccessManager::ASSIGN));
            if(!empty($firstProcessInstance->process->last)) {
                $formStageChildren = $this->renderAjax('_form_deal_update', [
                    'creater' => $permission['access_id'],
                    'processField' => $processField['processField'],
                    'processInstance' => new ProcessInstance(),
                    'messages' => $messages,
                    'assigned' => isset($previousPermission['access_id']) ? $previousPermission['access_id'] : '',
                ]);

                if (!$processInstance->isPermissinDealSuperAdmin()) {
                    if (!$this->isAllowedToEditStage($firstProcessInstance, AccessManager::UPDATE | AccessManager::ASSIGN,
                        $processInstanceId, $action)
                    ) {
                        $formStageChildren = $this->renderAjax('_form_deal_view', [
                            'modify' => ProcessTemplate::getLastProcess($firstProcessInstance->last) ? ($this->isModify((integer)$permission['access_id']) ? $buildingProcess->isModify() : false) : false,
                            'creater' => $permission['access_id'],
                            'processField' => $this->isAllowedToViewStage($firstProcessInstance) ? $processField['processField'] : [],
                            'processInstance' => $firstProcessInstance,
                            'messageProcessInstance' => $this->isAllowedToViewStage($firstProcessInstance) ? [] : self::getMessages([])[self::NOT_PERMISSION],
                            'messages' => $messages,
                            'assigned' => isset($previousPermission['access_id']) ? $previousPermission['access_id'] : '',
                        ]);
                    }

                }
            } else {
                $formStageChildren = $this->renderAjax('_form_deal_view', [
                    'modify' => false,
                    'processField' => [],
                    'processInstance' => $firstProcessInstance,
                    'messageProcessInstance' => self::getMessages([])[self::MESSAGE_FINAL_STAGE],
                    'messages' => $messages,
                ]);
            }

            $isAllowedToEditStage = $this->isAllowedToEditStage(
                $firstProcessInstance,
                AccessManager::UPDATE | AccessManager::ASSIGN,
                $processInstanceId,
                $action
            );

            return $this->render('_form_update', [
                'processInstances' => $processInstance->descendants,
                'firstProcessInstance' => $firstProcessInstance,
                'processInstance' => $processInstance,
                'formStageChildren' => $formStageChildren,
                'validationFormStage' => $processField['validationFormStage'],
                'modelDiscussion' => new Discussion(),
                'messages' => $messages,
                'userId' => Yii::$app->user->id,
                'discussionMessagesId' => $discussionMessagesId,
                'isAllowedToEditStage' => $isAllowedToEditStage
            ]);

           
            
        }
        $this->showMessage(self::MESSAGE_STAGE_NOT, null, 'warning');
        return $this->render('_form_update');
    }

    

    /**
     * Finds the BuildingProcess model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return ProcessInstance the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProcessInstance::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }


    /**
     * @param $messageId
     * @param array $options
     * @param string $type
     */
    private function showMessage($messageId, $options = [], $type = 'error')//todo
    {
        Yii::$app->getSession()->setFlash($type, [self::getMessages($options)[$messageId]]);
    }

    /**
     * @return string|Response
     */
    public function actionUpdate($id)
    {
        $processInstance = $this->findModel($id);

        $post = Yii::$app->request->post();
        if (!isset($post['Field'])) {
            return 'No fields are set! Return to the process configuration.';
        }
        $processInstance->processField($post['Field']);
        $buildingProcess = $this->findModelProcessTemplate($processInstance->process_id);
        $processFields = $buildingProcess->processField($processInstance);
        $permission = current($processInstance->getListAllAccessHolders(AccessManager::ASSIGN));
        $formStageChildren = $this->renderAjax('_form_deal_update', [
            'creater' => $permission['access_id'],
            'processField' => $processFields['processField'],
            'processInstance' => $processInstance,
        ]);
        if (!empty($processInstance->next) && !$processInstance->isPermissinDealSuperAdmin()) {
            if ($this->isModify((integer)$permission['access_id']) && $buildingProcess->isModify()) {
                return $this->redirect([
                    'view',
                    'id' => $processInstance->next->parent,
                    'type' => Yii::$app->request->get('type'),
                    'tab' => $processInstance->next->id,
                ]);
            }
        }

        if(ProcessTemplate::nextLast($processInstance->process)) {
            if(empty($processInstance->next)) {
                $finalProcessInstance = $processInstance->createFinalDeal($processInstance->process->next->id);
                return $this->redirect([
                    'view',
                    'id' => $finalProcessInstance->parent,
                    'type' => Yii::$app->request->get('type'),
                    'tab' => $finalProcessInstance->id,
                ]);
            }
            return $this->redirect([
                'view',
                'id' => $processInstance->next->parent,
                'type' => Yii::$app->request->get('type'),
                'tab' => $processInstance->next->id,
            ]);

        }


        return $this->renderAjax('_form_instance', [
            'formStageChildren' => $formStageChildren,
            'modelPermission' => new Permission(),
            'processInstance' => $processInstance,
            'processTemplates' => $processInstance->process->next,
        ]);

    }

    /**
     * Deletes an existing BuildingProcess model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @param string $type
     *
     * @return mixed
     */
    public function actionDelete($id, $type = null, $page = 1)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index', 'type' => $type, 'page' => $page]);
    }

    /**
     * @return array|void
     */
    public function actionDiscussionUpdate()
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        if (!Yii::$app->request->isAjax) {
            $response->statusCode = self::HTTP_STAT_BAD_REQUEST;
            $response->data = ['message' => 'method is not ajax'];
            return;
        }
        $request = Yii::$app->request->post();
        $modelId = (int)$request['modelDocumentId'];
        $messageText = (string)$request['Discussion']['text'];
        $channelType = (string)$request['Discussion']['channel'];
        $documentId = (int) $request['modelDocumentId'];
        $userId = (int) Yii::$app->user->id;
        $modelDiscussion = new Discussion();
        $modelDiscussion->saveNewDiscussionMessage($modelId, $messageText, $channelType);
        $modelDocument = Document::findOne($documentId);
        $messagesArray = Discussion::getSortedArrayMessages($modelDocument);
        $view = $this->renderAjax('_view_discussion_delete_message', [
            'messagesArray' => $messagesArray,
            'userId' => $userId
        ]);
        return [
            'view' => $view
        ];
    }

    /**
     * @param ProcessInstance $processInstance
     * @param $permission
     *
     * @return mixed
     */
    private function isAllowedToEditStage (ProcessInstance $processInstance, $permission, $index, $action)
    {
        if ($action === $index) {

            return $processInstance->checkPermission($permission);

        }
        return false;
    }

    /**
     * @param $access_id
     *
     * @return bool
     */
    private function isModify($access_id): bool
    {
        return Yii::$app->user->getId() === $access_id;
    }


    /**
     * @param ProcessInstance $processInstance
     *
     * @return bool
     */
    private function isAllowedToViewStage (ProcessInstance $processInstance)
    {
        $hasPermission =  $processInstance->checkPermission(AccessManager::ASSIGN|AccessManager::VIEW);
        $isViewStage = Yii::$app->user->identity->isAllowedToViewStage();

        return $hasPermission || $isViewStage;
    }

    /**
     * Delete message in Document cell with name Discussion
     * @return Json
     */
    public function actionDeleteDiscussionMessage()
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        if (!Yii::$app->request->isAjax) {
            $response->statusCode = self::HTTP_STAT_BAD_REQUEST;
            $response->data = ['message' => 'method is not ajax'];
            return;
        }
        $request = Yii::$app->request->post();
        $documentId = (int)$request['modelDocumentId'];
        $messageId = (int)$request['messageId'];
        $userId = (int) Yii::$app->user->id;
        $Discussion = new Discussion();
        $messagesArray = $Discussion->deleteMessage($documentId, $messageId );
        $form = $this->renderAjax('_view_discussion_delete_message', [
            'messagesArray' => $messagesArray,
            'userId' => $userId]);
        return [
            'view' => $form
        ];
    }

    /**
     * Create  Document node with name Discussion in parent folder
     * @return Json
     */
    public function actionDiscussionDocumentCreate()
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        if (!Yii::$app->request->isAjax) {
            $response->statusCode = self::HTTP_STAT_BAD_REQUEST;
            $response->data = ['message' => 'method is not ajax'];
            return;
        }
        $request = Yii::$app->request->post();
        $processId = isset($request['processId']) ? (int)$request['processId'] : (int)$request['processInstId'];
        $processInstance = ProcessInstance::findOne($processId);
        $ProcessFieldTemplate = Discussion::getProcessFieldTemplateByProcess($processInstance);
        $ProcessInstanceField = $processInstance->findModelProcessFieldInstance($ProcessFieldTemplate);
        $discussionMessagesId = $ProcessInstanceField->data;

        if ($discussionMessagesId) {
            $response->data = ['id' => $discussionMessagesId];
            return;
        }
        $parentFolderId = isset($request['parentFolderId']) ? (int)$request['parentFolderId'] : (int)$request['parentId'];
        $discussionMessagesId = Discussion::addDiscussionToParentFolder($parentFolderId);
        Discussion::updateProcessInstanceField($ProcessInstanceField, $discussionMessagesId);
        $response->data = ['id' => $discussionMessagesId, 'message' => 'Discussion document create'];
    }

    /**
     * @return string
     */
    public function actionDocument()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $this->renderAjax('_form_document');
    }

}
