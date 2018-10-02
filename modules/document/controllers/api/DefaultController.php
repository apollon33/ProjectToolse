<?php

namespace modules\document\controllers\api;

use modules\document\models\Document;
use modules\document\models\DocumentSearch;
use yii\helpers\Url;
use yii\rest\Controller;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;
use common\access\AccessManager;

class DefaultController extends Controller
{
    /**
     * Behavior of actions with documents
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['authMethods'] = [
            HttpBearerAuth::className(),
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['permission'],
                    'roles' => ['@'],
                ],
                [
                    'allow' => true,
                    'actions' => ['index', 'view', 'attachment'],
                    'roles' => ['document.backend.default:' . AccessManager::VIEW],
                ],
                [
                    'allow' => true,
                    'actions' => ['update'],
                    'roles' => ['document.backend.default:' . AccessManager::UPDATE],
                ],
                [
                    'allow' => true,
                    'actions' => ['create'],
                    'roles' => ['document.backend.default:' . AccessManager::CREATE],
                ],
                [
                    'allow' => true,
                    'actions' => ['delete'],
                    'roles' => ['document.backend.default:' . AccessManager::DELETE],
                ],

            ],
        ];
        return $behaviors;
    }
    /**
     * Displays documents
     * @return array
     */
    public function actionIndex()
    {
        $searchModel = new DocumentSearch();
        $query = $searchModel->search(Yii::$app->request->queryParams);

        if (!empty(Yii::$app->request->queryParams['page'])) {
            $query = $searchModel->search(array_merge(Yii::$app->request->queryParams, [
                'page' => 1 + (int)(Yii::$app->request->queryParams['page'] / Yii::$app->request->get('pageSize', $query->pagination->pageSize))
            ]));
        }

        return $query;
    }
    /**
     * Displays information about the document
     * @param int $id
     * @return object
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $document = Document::findOne($id);
        if ($document === null) {
            throw new NotFoundHttpException('Document is not found!');
        }
        if ($this->isDocumentAccess($document, AccessManager::VIEW | AccessManager::ASSIGN)) {
            return $document;
        } else {
            throw new NotFoundHttpException('Access is denied!');
        }
    }
    /**
     * Creates a new document and displays information about it
     * @return object
     * @throws ForbiddenHttpException
     */
    public function actionCreate()
    {
        $postData = Yii::$app->request->getBodyParams();
        $node = new Document();
        $node->activeOrig = $node->active;
        $node->load($postData, '');
        $nodeModel = null;

        if (!empty($postData['parentKey'])) {
            $nodeModel = Document::findOne($postData['parentKey']);
            if ($this->isDocumentAccess($nodeModel, AccessManager::CREATE)) {
                $node->appendTo($nodeModel);
            }
        } else {
            $node->makeRoot();
        }
        if ($node->save()) {
            if ($nodeModel === null || $nodeModel->user_id != Yii::$app->user->id) {
                $node->assignPermission(
                    AccessManager::VIEW |
                    AccessManager::UPDATE |
                    AccessManager::CREATE |
                    AccessManager::DELETE
                );
            }
            return $node;
        } else {
            return [
                'errors' => $node->errors,
            ];
        }
    }
    /**
     * Updates the current document and displays the updated information
     * @param int $id
     * @return object
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $document = Document::findOne($id);
        if ($document === null) {
            throw new NotFoundHttpException('Document is not found!');
        }
        if ($this->isDocumentAccess($document, AccessManager::UPDATE | AccessManager::ASSIGN)) {
            if ($document->load(Yii::$app->request->getBodyParams(), '') && $document->save()) {
                return $document;
            }
            else {
                return [
                    'errors' => $document->errors,
                ];
            }
        } else {
            throw  new NotFoundHttpException('Permission denied');
        }
    }
    /**
     * Deletes the current document
     * @param int $id
     * @return array
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionDelete($id)
    {
        $document = Document::findOne($id);
        if ($document === null) {
            throw new NotFoundHttpException('Document is not found!');
        }
        if ($this->isDocumentAccess($document, AccessManager::DELETE)) {
            if ($document->delete()) {
                return [
                    'status' => true,
                ];
            } else {
                throw new ServerErrorHttpException('Failed to delete object.');
            }
        } else {
            throw new NotFoundHttpException('Permission denied');
        }
    }

    /**
     * @param $id
     * @return $this
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionAttachment($id)
    {
        $document = Document::findOne($id);
        if ($document === null) {
            throw new NotFoundHttpException('Document is not found!');
        }
        if (array_key_exists('imageKey', Yii::$app->request->queryParams)) {
            $imageKey = Yii::$app->request->queryParams['imageKey'];
        } else {
            throw new NotFoundHttpException('Attachment information not sent');
        }
        if (!$document->isDocumentAccess(AccessManager::ASSIGN | AccessManager::VIEW)) {
            throw new ForbiddenHttpException("You cannot preview this attachment");
        }

        return Yii::$app->response->sendFile($document->path . "/" . $imageKey);
    }

    /**
     * @param Document $document
     * @param $permission
     * @return bool
     */
    private function isDocumentAccess(Document $document, $permission)
    {
        $hasDocument = $document->checkPermission($permission);
        $isOwner = $document->userid === Yii::$app->user->id;

        return $hasDocument || $isOwner;
    }

    /**
     * Displays permissions on actions with documents
     * @return array
     */
    public function actionPermission()
    {
        return [
            'GET /document' => Yii::$app->user->can('document.backend.default:' . AccessManager::VIEW),
            'GET /document/0' => Yii::$app->user->can('document.backend.default:' . AccessManager::VIEW),
            'POST /document' => Yii::$app->user->can('document.backend.default:' . AccessManager::CREATE),
            'PUT /document/0' => Yii::$app->user->can('document.backend.default:' . AccessManager::UPDATE),
            'DELETE /document/0' => Yii::$app->user->can('document.backend.default:' . AccessManager::DELETE),
            'GET /document/0/attachment' =>Yii::$app->user->can('document.backend.default:' . AccessManager::VIEW),
        ];
    }
}