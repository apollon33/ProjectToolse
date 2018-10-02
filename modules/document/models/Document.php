<?php

namespace modules\document\models;

use common\behaviors\NestedSetsDocumentBehavior;
use Yii;
use yii\helpers\ArrayHelper;
use kartik\tree\models\Tree;
use common\models\User;
use kartik\tree\TreeView;
use common\behaviors\ImageBehavior;
use common\behaviors\TimestampBehavior;
use common\behaviors\AccessBehavior;
use common\behaviors\CreatorBehavior;
use common\access\AccessInterface;
use common\access\AccessManager;
use modules\buildingprocess\models\ProcessTemplate;
use yii\db\{Query, ActiveQuery, ActiveQueryInterface};
use common\behaviors\FilesBehavior;
use common\models\Attachment;
use common\access\AccessUserApplicant;
use common\access\AccessRoleApplicant;
use yii\validators\FileValidator;


/**
 * This is the model class for table "{{%document}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $root
 * @property integer $lft
 * @property integer $rgt
 * @property integer $lvl
 * @property string $name
 * @property string $description
 * @property string $file
 * @property string $icon
 * @property integer $icon_type
 * @property integer $active
 * @property integer $selected
 * @property integer $disabled
 * @property integer $readonly
 * @property integer $visible
 * @property integer $collapsed
 * @property integer $movable_u
 * @property integer $movable_d
 * @property integer $movable_l
 * @property integer $movable_r
 * @property integer $removable
 * @property integer $removable_all
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $document_type
 */
class Document extends Tree implements AccessInterface
{

    /**
     *Id parent folder in which the folder will be created
     */
    const ID_FOLDER_PARENT = 1;
    const NODE_TYPE_DOCUMENT = 1;
    const NODE_TYPE_DISCUSSION = 2;
    const NODE_TYPE_ATTACHMENT = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%document}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $module = TreeView::module();
        $settings = ['class' => NestedSetsDocumentBehavior::className()] + $module->treeStructure;
        $parentBehaviors = empty($module->treeBehaviorName) ? [$settings] : [$module->treeBehaviorName => $settings];
        return array_merge($parentBehaviors, [
            TimestampBehavior::className(),
            CreatorBehavior::className(),
            [
                'class' => FilesBehavior::className(),
                'fieldName' => 'file',
                'inputFileName' => 'attachmentFiles',
            ],
            AccessBehavior::className(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'root', 'lft', 'rgt', 'lvl', 'icon_type', 'active', 'selected', 'disabled', 'readonly', 'visible', 'collapsed', 'movable_u', 'movable_d', 'movable_l', 'movable_r', 'removable', 'removable_all', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'required'],
            [['order', 'document_type'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['file'], 'string', 'max' => 128],
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf, docx, doc, odt, xlsx, xls, ods, txt, ico, png, jpg , zip, tar.gz, rar, psd, ppt, pptx','maxFiles' => 10, 'maxSize' => (new FileValidator())->getSizeLimit()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User'),
            'root' => Yii::t('app', 'Root'),
            'lft' => Yii::t('app', 'Lft'),
            'rgt' => Yii::t('app', 'Rgt'),
            'lvl' => Yii::t('app', 'Lvl'),
            'order' => Yii::t('app', 'Order'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'file' => Yii::t('app', 'Attachment'),
            'icon' => Yii::t('app', 'Icon'),
            'icon_type' => Yii::t('app', 'Icon Type'),
            'active' => Yii::t('app', 'Active'),
            'selected' => Yii::t('app', 'Selected'),
            'disabled' => Yii::t('app', 'Disabled'),
            'readonly' => Yii::t('app', 'Readonly'),
            'visible' => Yii::t('app', 'Visible'),
            'collapsed' => Yii::t('app', 'Collapsed'),
            'movable_u' => Yii::t('app', 'Movable U'),
            'movable_d' => Yii::t('app', 'Movable D'),
            'movable_l' => Yii::t('app', 'Movable L'),
            'movable_r' => Yii::t('app', 'Movable R'),
            'removable' => Yii::t('app', 'Removable'),
            'removable_all' => Yii::t('app', 'Removable All'),
            'created_at' => Yii::t('app', 'Created'),
            'updated_at' => Yii::t('app', 'Updated'),
            'created' => Yii::t('app', 'Created'),
            'updated' => Yii::t('app', 'Updated'),
        ];
    }

    /**
     * Return fully qualified query for document listing with permission checks
     * @todo There is known rare case issue with permission stripping when forbid access for your folders down in hierarchy
     * @return ActiveQueryInterface
     */
    public static function queryWithPermissions()
    {
        
        if (Yii::$app->authManager->checkAccess(Yii::$app->user->id, User::ROLE_SUPER_ADMIN)) {
            return Document::find()->addOrderBy('order, lft');
        }
        $accessManager = new AccessManager();
        $applicants = $accessManager->applicantsReceipt(Yii::$app->user->id);
        $userList = array_map(function ($a) {return $a->getUId();}, array_filter($applicants, function ($i) {return $i->getUType() === AccessInterface::USER;}));
        $groupList = array_map(function ($a) {return $a->getUId();}, array_filter($applicants, function ($i) {return $i->getUType() === AccessInterface::GROUP;}));
        
        /**
         * This query will return list of all available for user child nodes with reference to permission implementing father
         * @var \yii\db\Query $subQuery
         */ 
        $subQuery = (new Query())
            ->select([
                'd.id',
                'd.name',
                'c.root',
                'MAX(`c`.`lft`) AS lft',
                'MIN(`c`.`rgt`) AS rgt',
                'MAX(`c`.`lvl`) AS lvl',
            ])
            ->from(Document::tableName() . ' AS d')
            ->innerJoin('(' . Document::tableName() . ' c, ' . $accessManager->itemTable . ' ca)', [
                'and',
                'd.root=c.root',
                'd.lft>c.lft',
                'd.rgt<c.rgt',
                [
                    'and',
                    'c.id=ca.instance_id',
                    ['or',
                        ['and',
                            ['ca.access_type' => AccessInterface::USER],
                            ['ca.access_id' => $userList],
                        ],
                        ['and',
                            ['ca.access_type' => AccessInterface::GROUP],
                            ['ca.access_id' => $groupList],
                        ],
                    ],
                ],
            ])
            ->groupBy('d.id');
        
        return Document::find()
        ->from(Document::tableName() . ' AS doc')
            ->leftJoin(['j' => $subQuery], [
                'and',
                'doc.id=j.id',
            ])
            // get permissions on subquery folders
            ->leftJoin('(' . Document::tableName() . ' f, ' . $accessManager->itemTable . ' fa)', [
                'and',
                'j.lft=f.lft',
                'j.rgt=f.rgt',
                'j.lvl=f.lvl',
                [
                    'and',
                    'f.id=fa.instance_id',
                    ['or',
                        ['and',
                            ['fa.access_type' => AccessInterface::USER],
                            ['fa.access_id' => $userList],
                        ],
                        ['and',
                            ['fa.access_type' => AccessInterface::GROUP],
                            ['fa.access_id' => $groupList],
                        ],
                    ],
                ],
            ])
            // allow access to any parent folders that have children with access granted
            ->leftJoin('(' . Document::tableName() . ' p, ' . $accessManager->itemTable . ' pa)', [
                'and',
                'doc.root=p.root',
                'doc.lft<p.lft',
                'doc.rgt>p.rgt',
                [
                    'and',
                    'p.id=pa.instance_id',
                    ['or',
                        ['and',
                            ['pa.access_type' => AccessInterface::USER],
                            ['pa.access_id' => $userList],
                        ],
                        ['and',
                            ['pa.access_type' => AccessInterface::GROUP],
                            ['pa.access_id' => $groupList],
                        ],
                    ],
                    ['&', 'pa.permission', AccessManager::ANY_READ]
                ],
            ])
            // direct access permissions on documents
            ->leftJoin($accessManager->itemTable . ' a', [
                'and',
                'doc.id=a.instance_id',
                ['or',
                    ['and',
                        ['a.access_type' => AccessInterface::USER],
                        ['a.access_id' => $userList],
                    ],
                    ['and',
                        ['a.access_type' => AccessInterface::GROUP],
                        ['a.access_id' => $groupList],
                    ],
                ],
            ])
            ->where([
                'or',
                ['&', 'a.permission', AccessManager::ANY_READ],
                ['&', 'pa.permission', AccessManager::ANY_READ],
                [
                    'and',
                    [
                        'or',
                        ['&', 'a.permission', AccessManager::ANY_READ],
                        ['a.permission' => null]
                    ],
                    ['&', 'fa.permission', AccessManager::ANY_READ],
                ],
            ])
            ->groupBy(['doc.id'])
            ->addOrderBy('doc.order, doc.lft');
    }

    /**
     * Create a folder in the document tree
     * @param BuildingProcess
     */
    public function createFolderRoot($modelBuildingProcess)
    {
        if (self::findOne($modelBuildingProcess->file_url)) {
            return $modelBuildingProcess->file_url;
        }
        $this->name = $modelBuildingProcess->title;
        $parent = self::findOne(self::ID_FOLDER_PARENT);
        $this->appendTo($parent);
        $accessManager = new AccessManager();
        $applicant = new AccessUserApplicant(Yii::$app->user->getId());
        $accessManager->assignPermissions($this, $applicant, AccessManager::CRUD | AccessManager::MANAGE);

        return $this->id;
    }

    /**
     * Create a folder in the document tree
     *
     * @param $name
     * @param ProcessTemplate $modelBuildingProcess
     */
    public function createFolderChild($name, ProcessTemplate $modelBuildingProcess)
    {
        $this->name = $name;
        $parent = self::findOne($modelBuildingProcess->file_url);
        $this->appendTo($parent);
        $this->assignPermission(AccessManager::CRUD | AccessManager::MANAGE);
        $this->applicant = new AccessRoleApplicant(User::ROLE_SALES_MANAGER);
        $this->assignPermission(AccessManager::CRUD | AccessManager::MANAGE);

        return $this->id;
    }

    /**
     * @param int documentId
     * todo
     */
    public static function deleteDocument($documentId)
    {
        $document = self::findOne($documentId);
        if(!empty($document)) {
            $document->deleteWithChildren();
        }

    }

    /**
     * @param $documentId
     * @param $name
     */
    public static function renameDocument($documentId, $name)
    {
        $document = Document::findOne($documentId);
        if($document->name !== $name) {
            $document->name = $name;
            $document->save();
        }
    }

    /**
     * save session open document
     */
    public function setSelectedNodeData()
    {
        $postData = Yii::$app->request->post();

        if (Yii::$app->has('session')) {
            $module = TreeViewQMS::module();
            $keyAttr = $module->dataStructure['keyAttribute'];
            $session = Yii::$app->session;
            $session->set(ArrayHelper::getValue($postData, 'nodeSelected', 'kvNodeId'), $this->{$keyAttr});
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return int
     */
    public function getUId() : string
    {
        return $this->id;
    }

    /**
     * @return null
     */
    public function getUType()
    {
        return null;
    }

    /**
     * @return AccessInterface
     */
    public function getUParent()
    {
        return $this->parents(1)->one();
    }

    /**
     * @return null
     */
    public function getUPrefix() : string
    {
        return '';
    }

    /**
     *check is he owner
     * @return bool
     */
    public function isUOwner()
    {
        return $this->userid === Yii::$app->user->id;
    }

    /**
     * return owner of this document
     * @return User
     */
    public function getUOwner()
    {
        return User::findOne($this->userid);
    }

    /**
     * @return bool
     */
    public function isPermission()
    {
        $owner = $this->isUOwner();
        $super_admin = Yii::$app->user->identity->isSuperAdmin();
        return $owner || $super_admin;
    }

    /**
     *
     */
    public static function getName($id)
    {
        $document = Document::findOne($id);

        return $document->name;
    }

    /**
     * @return array
     */
    public function listPermissions()
    {
        $listPermission = [];
        $document = $this;
        while ($document !== null) {
            $listPermission = ArrayHelper::merge($listPermission, $document->listAllAccessHolders);
            $document = $document->getUParent();
        }
        $listPermission = ArrayHelper::index(array_reverse($listPermission), 'access_id');

        return $listPermission;
    }

    /**
     * @param array $listPermission
     *
     * @return Document[]
     */
    public function listDocument(array $listPermission) : array
    {
        $listPermission = array_keys( ArrayHelper::index($listPermission, 'instance_id'));
        $listDocument = Document::findAll($listPermission);

        return ArrayHelper::index($listDocument, 'id');
    }

    /**
     * Removes html tags and whitespace characters
     * @return string
     */
    public function getPureDescription()
    {
        return trim(strip_tags($this->description, '<a>'));
    }
    /**
     * @return array
     */
    public function fields()
    {
        $fields = parent::fields();
        unset($fields['selected'], $fields['disabled'], $fields['readonly'], $fields['visible'], $fields['collapsed'], $fields['movable_u'], $fields['movable_d'], $fields['movable_l'], $fields['movable_r'], $fields['removable'], $fields['removable_all'], $fields['created_at'], $fields['updated_at']);
        return array_merge($fields, [
            'description' => 'pureDescription',
            'attachments',

        ]);
    }

    /**
     * @return  ActiveQuery $models
     */
    public function getAttachments()
    {
        return $this->hasMany(Attachment::className(), ['id' => 'attachment_id'])
            ->viaTable('attachment_document', ['document_id' => 'id']);
    }
}