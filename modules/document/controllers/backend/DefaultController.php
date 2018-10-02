<?php

namespace modules\document\controllers\backend;
use common\models\AttachmentEntity;
use kartik\tree\TreeView;
use modules\document\models\Attachment;
use modules\document\models\Permission;
use modules\document\models\TreeViewQMS;
use Yii;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use modules\document\models\Document;
use common\access\AccessManager;
use common\access\AccessApplicant;
use common\access\AccessInterface;
use yii\helpers\ArrayHelper;
use common\controllers\BaseController;
use common\models\User;
use modules\buildingprocess\models\Discussion;
use yii\web\ForbiddenHttpException;

/**
 * DefaultController implements the CRUD actions for Document model.
 */
class DefaultController extends BaseController
{

    const NOT_PERMISSION = 4;
    const ERROR_PERMISSION = 5;

    /**
     * @param $options
     *
     * @return array
     */
    private static function getMessages($options)
    {
        return [
            self::NOT_PERMISSION => Yii::t('app', 'Permission denied'),
            self::ERROR_PERMISSION => Yii::t('app', 'An unexpected error occurred.'),
        ];
    }

    public function permissionMapping()
    {
        return [
            'index' => AccessManager::VIEW,
            'view' => AccessManager::UPDATE,
            'document-preview' => AccessManager::VIEW,
            'document-move' => AccessManager::VIEW,
            'document-permission' => AccessManager::UPDATE,
            'permission-remove' => AccessManager::DELETE,
            'document-remove' => AccessManager::DELETE,
            'document-create-root' => AccessManager::UPDATE,
            'document-update' => AccessManager::UPDATE,
            'document-create' => AccessManager::CREATE,
            'update' => AccessManager::UPDATE,
            'create' => AccessManager::CREATE,
            'delete' => AccessManager::DELETE,
            'delete-list' => AccessManager::DELETE,
            'type' => AccessManager::UPDATE,
            'attachment' => AccessManager::VIEW,
            'delete-attachment-file' => AccessManager::DELETE,
        ];
    }

    /**
     * Lists all Document models.
     *
     * @return mixed
     */
    public function actionIndex($id = null)
    {
        return $this->render('index', [
            'id' => $id,
        ]);
    }

    /**
     * @return array json
     */
    public function actionType($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (intval($id) === AccessInterface::USER) {
            return User::getList();
        } else {
            return ArrayHelper::map(User::getListRoles(), 'name', 'name');
        }
    }

    /**
     * assigning rights to a folder or file
     *
     * @return array|Response
     * @throws NotFoundHttpException
     */
    public function actionPermissionRemove($id, $access_type, $access_id, $permission)
    {
        $currUrl = '/document';
        $nodeModel = self::findModel($id);

        if ($nodeModel->checkPermission(AccessManager::MANAGE)) {

            if($permission) {
                $sumPermission = 0;
                $nodeModel->applicant = new AccessApplicant($access_id, $access_type);
                $nodeModel->assignPermission($sumPermission);

                return $this->redirect(['index', 'id' => $id]);
            }

            $nodeModel->applicant = new AccessApplicant($access_id, $access_type);

            $nodeModel->removePermission();

            return $this->redirect(['index', 'id' => $id]);
        }
        $this->showMessage(self::NOT_PERMISSION, null, 'error');

        return $this->redirect($currUrl);

    }

    /**
     * assigning rights to a folder or file
     *
     * @return array|Response
     * @throws NotFoundHttpException
     */
    public function actionDocumentPermission()
    {
        $postData = Yii::$app->request->post();
        $sumPermission = null;
        $nodeModel = self::findModel($postData['id']);

        if ($nodeModel->checkPermission(AccessManager::UPDATE)) {
            $permission = new Permission();
            $permission->load(Yii::$app->request->post());
            $sumPermission = !empty($postData['permission']) ? array_sum($postData['permission']) : 0;
            $nodeModel->applicant = new AccessApplicant ($permission->list, $permission->type);
            $nodeModel->assignPermission($sumPermission);

            $nodeModel->setSelectedNodeData();

            return $this->redirect(['index']);
        }
        $this->showMessage(self::NOT_PERMISSION, null, 'error');

        return $this->redirect(['index']);

    }

    public function actionDocumentCreateRoot()
    {
        $postData = Yii::$app->request->post();
        if (Yii::$app->user->identity->isAdmin() || Yii::$app->user->identity->isSuperAdmin()) {
            $currUrl = $postData['currUrl'];
            $node = new Document();
            $node->activeOrig = $node->active;
            $node->load($postData);
            if ( $node->makeRoot()) {
                $node->assignPermission(
                    AccessManager::CRUD |
                    AccessManager::MANAGE);
                $node->setSelectedNodeData();

                return $this->redirect($currUrl);
            }
            $this->showMessage(self::ERROR_PERMISSION, null, 'error');

            return $this->redirect($currUrl);
        }
        $this->showMessage(self::NOT_PERMISSION, null, 'error');

        return $this->redirect(['index']);
    }

    /**
     * Create a new folder or file
     *
     * @throws NotFoundHttpException
     * @return array|Response
     */
    public function actionDocumentCreate()
    {
        $postData = Yii::$app->request->post();
        $parentKey = $postData['parentKey'];
        $nodeModel = self::findModel($parentKey);

        if ($nodeModel->checkPermission(AccessManager::CREATE)) {
            $currUrl = $postData['currUrl'];
            $node = new Document();
            if ((integer)$postData['nodesType'] === Document::NODE_TYPE_ATTACHMENT) {
                $node = new Attachment();
            }
            $node->activeOrig = $node->active;
            $node->load($postData);
            if ($node->appendTo($nodeModel)) {
                $node->assignPermission(
                    AccessManager::ADMIN
                );
                $node->setSelectedNodeData();

                return $this->redirect($currUrl);
            }
            $this->showMessage(self::ERROR_PERMISSION, null, 'error');

            return $this->redirect($currUrl);

        }
        $this->showMessage(self::NOT_PERMISSION, null, 'error');

        return $this->redirect(['index']);
    }

    /**
     * update file data
     *
     * @param $id
     *
     * @throws NotFoundHttpException
     * @return array|Response
     */
    public function actionDocumentUpdate($id)
    {
        $postData = Yii::$app->request->post();

        $currUrl = $postData['currUrl'];
        $node = $this->findModel($id);
        if ((integer)$postData['nodesType'] === Document::NODE_TYPE_ATTACHMENT) {
            $node = $this->findModuleAttachment($id);
        }
        if ($node->checkPermission(AccessManager::ANY_WRITE)) {
            if ($node->load(Yii::$app->request->post()) && $node->save()) {
                $node->setSelectedNodeData();
                return $this->redirect($currUrl);
            } else {
                $this->showMessage(self::ERROR_PERMISSION, null, 'error');

                return $this->redirect($currUrl);
            }
        }
        $this->showMessage(self::NOT_PERMISSION, null, 'error');
        return $this->redirect(['index']);

    }

    /**
     * Deleting a file or folder
     * @return array|Response
     * @throws NotFoundHttpException
     */
    public function actionDocumentRemove()
    {
        $postData = Yii::$app->request->post();
        $id = $postData['id'];
        $currUrl = '/document';
        if (empty($id)) {
            $this->showMessage(self::ERROR_PERMISSION, null, 'error');

            return $this->redirect($currUrl);
        }
        $nodeModel = self::findModel($id);
        if ($nodeModel->checkPermission(AccessManager::DELETE)) {
            $nodeModel->removePermission();

            $nodeModel->removeNode(false);

            return $this->redirect($currUrl);
        }
        $this->showMessage(self::NOT_PERMISSION, null, 'error');

        return $this->redirect($currUrl);


    }

    /**
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionDocumentPreview()
    {
        $modelPermission = new Permission();
        $postData = Yii::$app->request->post();
        $id = $postData['id'];
        $parentKey = $postData['parentKey'];
        Yii::$app->response->format = "json";
        $listPermission = [];
        $isPermission = false;
        $permission = [];
        $documents = [];
        $root = false;
        $update = false;
        if (empty($id)) {
            if (intval($parentKey)) {
                $nodeModel = self::findModel($parentKey);
                if (!$nodeModel->checkPermission(AccessManager::CREATE)) {
                    return [
                        "out" => Yii::t('app', 'Permission denied'),
                        "status" => "error",
                    ];

                }

                $nodeModel = new Document();
                $update = true;
            } else {
                $nodeModel = new Document();
                $root = true;
                $update = true;
            }
        } else {

            $nodeModel = self::findModel($id);
            if (empty($nodeModel)) {
                return [
                    "out" => Yii::$app->response->statusCode,
                    "status" => "error",
                ];
            }
            if (!$nodeModel->checkPermission(AccessManager::ANY_READ)) {

                return [
                    "out" => Yii::t('app', 'Permission denied'),
                    "status" => "error",
                ];
            }
            if ($nodeModel->checkPermission(AccessManager::ANY_WRITE)) {
                $update = true;
            }
            $isPermission = $nodeModel->isPermission() ? true : false;

            if($isPermission) {
                $listPermission = $nodeModel->listPermissions();
                $permission = $nodeModel->permissions;
                $documents = $nodeModel->listDocument($listPermission);
            }
        }
        $messages = Discussion::getSortedArrayMessages($nodeModel);
        if (!empty($messages) || is_array($messages)) {
            $update = false;
        }
        $nodeView = '@modules/document/views/backend/default/_tabs';
        $nodeType = isset($nodeModel->document_type) ? $nodeModel->document_type: $postData['fileType'];
        if ($nodeType == Document::NODE_TYPE_ATTACHMENT) {
            $nodeModel = new Attachment();
            if (!empty($id)) {
                $nodeModel = $this->findModuleAttachment($id);
            }
            $nodeModel->scenario = Attachment::SCENARIO_ATTACHMENT;
        }
        $params = [
            'node' => $nodeModel,
            'update' => $update,
            'permission' => $permission,
            'isPermission' => $isPermission,
            'documents' => $documents,
            'modelPermission' => $modelPermission,
            'listPermission' => $listPermission,
            'root' => $root,
            'parentKey' => $parentKey,
            'action' => $postData['formAction'],
            'formOptions' => empty($formOptions) ? [] : $formOptions,
            'modelClass' => $postData['modelClass'],
            'currUrl' => $postData['currUrl'],
            'isAdmin' => $postData['isAdmin'],
            'iconsList' => $postData['iconsList'],
            'softDelete' => $postData['softDelete'],
            'showFormButtons' => $postData['showFormButtons'],
            'showIDAttribute' => $postData['showIDAttribute'],
            'allowNewRoots' => $postData['allowNewRoots'],
            'nodeView' => $nodeView,
            'nodeAddlViews' => empty($nodeAddlViews) ? [] : $nodeAddlViews,
            'nodeSelected' => $postData['nodeSelected'],
            'breadcrumbs' => empty($breadcrumbs) ? [] : $breadcrumbs,
            'nodeType' => $nodeType ,
            'messages' => $messages,
            'modelDiscussion' => new Discussion(),
            'userId' => Yii::$app->user->id,
            'attachmentDocument' => new AttachmentEntity(),
        ];

        return [
            "out" => $this->renderAjax($nodeView, ['params' => $params]),
            "status" => "success",
            "nodeType" => $nodeType,
        ];
    }

    /**
     * @return array
     */
    public function actionDocumentMove()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $dir = $idFrom = $idTo = $treeMoveHash = null;
        $postData = Yii::$app->request->post();
        $allowNewRoots = false;
        $idFrom = $postData['idFrom'];
        $idTo = $postData['idTo'];
        $dir = $postData['dir'];
        $allowNewRoots = $postData['allowNewRoots'];
        $nodeFrom = Document::findOne($idFrom);
        $nodeTo = Document::findOne($idTo);
        if ($nodeFrom->checkPermission(AccessManager::ANY_WRITE)) {
            if ($nodeTo->checkPermission(AccessManager::CREATE | AccessManager::ASSIGN)) {
                $isMovable = $nodeFrom->isMovable($dir);
                if (!empty($nodeFrom) && !empty($nodeTo)) {
                    if (!$isMovable) {
                        return false;
                    }
                    if ($dir == 'u') {
                        $nodeFrom->insertBefore($nodeTo);
                    } elseif ($dir == 'd') {
                        $nodeFrom->insertAfter($nodeTo);
                    } elseif ($dir == 'l') {
                        if ($nodeTo->isRoot() && $allowNewRoots) {
                            $nodeFrom->makeRoot();
                        } else {
                            $nodeFrom->insertAfter($nodeTo);
                        }
                    } elseif ($dir == 'r') {
                        $nodeFrom->appendTo($nodeTo);
                    }
                    $nodeFrom->save();
                }

                $out = Yii::t('app', 'The node was moved successfully.');
                
                return ['out' => $out, 'status' => 'success'];
            }
            $out = Yii::t('app', 'Permission move denied');

            return [
                "out" => $out,
                "status" => "error",
            ];
        }
        $out = Yii::t('app', 'Permission denied');

        return [
            "out" => $out,
            "status" => "error",
        ];
        
    }

    /**
     * @param $id
     * @param $imageKey
     * @return $this
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionAttachment($id, $imageKey)
    {
        
        $document = $this->findModel($id);
        if (!$document->checkPermission(AccessManager::ANY_READ)) {
            throw new ForbiddenHttpException("You cannot preview this attachment");
        }
        $path = $document->path . "/" . $imageKey;
        return Yii::$app->response->sendFile($document->path . "/" . $imageKey);
    }

    /**
     * Displays a single Document model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDownload($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the Document model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Document the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Document::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    /**
     * Finds the Document model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Document the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModuleAttachment($id)
    {
        if (($model = Attachment::findOne($id)) !== null) {
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
    private function showMessage($messageId, $options = [], $type = 'error')
    {
        Yii::$app->getSession()->setFlash($type, [self::getMessages($options)[$messageId]]);
    }

    /**
     * Delete attachment file
     * @return array|void|Response
     */
    public function actionDeleteAttachmentFile()
    {
        
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        if (!Yii::$app->request->isAjax) {
            $response->data = ['message' => 'method is not ajax'];
            return;
        }
        $request = Yii::$app->request;
        $fileId = (string)$request->post('key');
        $documentId = (integer)$request->post('documentId');
        $document = Document::findOne($documentId);
        if ($document->checkPermission(AccessManager::ANY_WRITE) && Yii::$app->user->identity->isSuperAdmin()) {
            $document->deleteAttachmentFileById($fileId);
            if (!$document->save()) {
                $response->statusCode = self::HTTP_STATUS_UNPROCESSABLE_ENTITY;
                $response->data = ['errors' => $document->getErrors()];
                return;
            }
            return $response->data = ['message' => 'Attachment file delete'];
        }
        $response->statusCode = self::HTTP_STATUS_UNPROCESSABLE_ENTITY;
        return $response->data = ['message' => 'Permission denied'];
    }
}
