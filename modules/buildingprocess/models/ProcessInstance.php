<?php

namespace modules\buildingprocess\models;

use common\access\AccessUserApplicant;
use common\models\User;
use modules\document\models\Document;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

use common\access\AccessManager;
use common\access\AccessInterface;
use common\access\AccessApplicant;

use modules\field\models\ProcessFieldTemplate;
use modules\field\models\ProcessFieldInstance;

use common\behaviors\AccessBehavior;
use common\behaviors\TimestampBehavior;

use yii\db\Query;

/**
 * This is the model class for table "{{%process_instance}}".
 *
 * @property integer $id
 * @property integer $process_id
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property ProcessTemplate $process
 */
class ProcessInstance extends ActiveRecord implements AccessInterface
{

    public $name;

    const ACCESS_TYPE = 1;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            TimestampBehavior::className(),
            AccessBehavior::className(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%process_instance}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['process_id'], 'required'],
            [['name'], 'string'],
            [['process_id', 'created_at', 'updated_at', 'owner', 'file_url', 'parent'], 'integer'],

            [
                ['file_url'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Document::className(),
                'targetAttribute' => ['file_url' => 'id'],
            ],
            [
                ['owner'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => ['owner' => 'id'],
            ],
            [
                ['process_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ProcessTemplate::className(),
                'targetAttribute' => ['process_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'process_id' => Yii::t('app', 'Process'),
            'file_url' => Yii::t('app', 'File Url'),
            'parent' => Yii::t('app', 'parent'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'owner' => Yii::t('app', 'Owner'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocument()
    {
        return $this->hasMany(Document::className(), ['id' => 'file_url']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcess()
    {
        return $this->hasOne(ProcessTemplate::className(), ['id' => 'process_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcessFieldTemplates()
    {
        return $this->hasMany(ProcessFieldTemplate::className(), ['process_id' => 'process_id']);
    }

    /**
     * @return array
     */
    public function getDescendants()
    {
        return $this->hasMany(self::className(), ['parent' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcessFieldTemplate()
    {
        return $this->hasOne(ProcessFieldTemplate::className(), ['process_id' => 'process_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcessFieldInstances()
    {
        return $this->hasMany(ProcessFieldInstance::className(), ['instance_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcessFieldInstance()
    {
        return $this->hasOne(ProcessFieldInstance::className(), ['instance_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwners()
    {
        return $this->hasOne(User::className(), ['id' => 'owner']);
    }

    /**
     * @param $type
     *
     * @return \yii\db\ActiveQuery
     */
    public static function getUserDealsQueryByProcessTemplateType($type)
    {
        $query = ProcessInstance::find()
            ->from(ProcessInstance::tableName() . ' AS deal')
            ->innerJoin('`process_template`','`process_template`.`id`  = `deal`.`process_id`')
            ->innerJoin(ProcessInstance::tableName() . ' pic', [
                'or',
                'deal.id=pic.parent',
                [
                    'and',
                    'deal.id=pic.id',
                    ['pic.parent' => null]
                ],
            ]);

        if (!Yii::$app->authManager->checkAccess(Yii::$app->user->id, User::ROLE_SUPER_ADMIN)) {

            $accessManager = new AccessManager();
            $applicants = $accessManager->applicantsReceipt(Yii::$app->user->id);
            foreach ($applicants as $applicant) {
                if ($applicant->getUType() === AccessInterface::USER) {
                    $userList[] = $applicant->getUId();
                }
                if ($applicant->getUType() === AccessInterface::GROUP) {
                    $groupList[] = $applicant->getUId();
                }
            }

            $query = $query
                ->innerJoin($accessManager->itemTable . ' ca', [
                'and',
                'CONCAT("deal-",pic.id)=ca.instance_id',
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
                ['&', 'ca.permission',AccessManager::ASSIGN | AccessManager::VIEW]
            ]);

        }
        return $query
            ->where(['`process_template`.`type`' => $type])
            ->groupBy(['deal.id']);

    }

    /**
     * create final deal
     * @param $processId
     *
     * @return ProcessInstance
     */
    public function createFinalDeal($processId)
    {
        $processInstance = new ProcessInstance();
        $processInstance->process_id = $processId;
        $processInstance->parent = $this->parent;
        $processInstance->save();
        $processInstance->assignPermission(AccessManager::ASSIGN);
        return $processInstance;

    }

    /**
     * @param $fields
     *
     * @return array
     */
    public function processField($fields)
    {
        $processField = [];
        foreach ($fields as $id => $fieldValue) {
            $processFieldTemplate = $this->findModelProcessFieldTemplate($id);
            $field = $processFieldTemplate->createField();
            $postData = $field->load($fieldValue, $this, $processFieldTemplate, $field->attachmentType);
            $processFieldInstance = $this->findModelProcessFieldInstance($processFieldTemplate);
            if ($processFieldInstance->load($postData) && $processFieldInstance->save()) {
                $processField[] = $processFieldTemplate->createField($this);
            }
        }

        return $processField;
    }

    /**
     * @return bool
     */
    public function beforeDelete()
    {
        $processFieldInstance= new ProcessFieldInstance();
        if (!parent::beforeDelete()) {
            return false;
        }
        if($this->process->processFieldTemplatesFieldLink) {
            $processFieldInstance->deleteDocumentFieldLink($this, $this->process->processFieldTemplatesFieldLink);
        }
        self::deleteAll('parent = ' . $this->id);

        return true;
    }

    /**
     * @param bool $insert
     *
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if(empty($this->getUParent())) {
                $this->owner = Yii::$app->user->id;
            } else {
                $this->owner = $this->getUParent()->owner;
            }



            return true;
        }

        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (empty($this->parent)) {
            if(!empty($this->process->parents)) {
                $processInstance = new ProcessInstance();
                $processInstance->process_id = $this->process->parents[0]->id;
                $processInstance->parent = $this->id;
                $processInstance->save();
                $processInstance->assignPermission(AccessManager::ASSIGN);
            }
        }
    }

    /**
     * @return array|null|ActiveRecord
     */
    public function getLast()
    {
        return ProcessInstance::find()
            ->where(['parent' => (int)$this->parent])
            ->orderBy('id DESC')->one();
    }

    /**
     * @return array|null|ActiveRecord
     */
    public function getPrev()
    {
        return ProcessInstance::find()
            ->where(['parent' => (int)$this->parent])
            ->andWhere(['<', 'id', $this->id,])
            ->orderBy('id DESC')->one();
    }




    /**
     * @param array $permission
     */
    public function appointmentPermissionToStage($permission)
    {
        $listPermission = current($this->getListAllAccessHolders(AccessManager::ASSIGN));
        if($listPermission) {
            $this->applicant = new AccessUserApplicant($listPermission['access_id']);
            $this->assignPermission(0);
        }

        $this->applicant = new AccessApplicant($permission['list'], $permission['type']);
        $this->assignPermission(AccessManager::ASSIGN);
    }

    /**
     * @return bool
     */
    public function isPermissinDealSuperAdmin()
    {
        return Yii::$app->user->identity->getRoleName() == User::ROLE_SUPER_ADMIN;
    }

    /**
     * @return array|null|ActiveRecord
     */
    public function getNext()
    {
        return ProcessInstance::find()
            ->where(['process_id' => $this->process_id])
            ->where(['parent' => $this->parent])
            ->andWhere(['>', 'id', $this->id,])->one();
    }

    /**
     * @return mixed
     */
    public function getNextId()
    {
        return $this->getNext()->id;
    }

    /**
     * @inheritdoc
     */
    public function getUId() : string
    {
        return $this->getUPrefix() . $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getUType()
    {
        return self::ACCESS_TYPE;
    }

    /**
     * @inheritdoc
     */
    public function getUParent()
    {
        return self::findOne((int)$this->parent);
    }

    /**
     * @inheritdoc
     */
    public function getUPrefix() : string
    {
        return 'deal-';
    }

    /**
     * @return bool
     */
    public function isUOwner()
    {
        return false;
    }

    /**
     * return owner of this document
     * @return AccessInterface
     */
    public function getUOwner()
    {
        return null;
    }

    /**
     * @param $id
     */
    protected function findModelProcessFieldTemplate($id)
    {
        return ProcessFieldTemplate::findOne($id);
    }

    /**
     * @param ProcessFieldTemplate $processFieldTemplate
     *
     * @return ProcessFieldInstance|static
     */
    public function findModelProcessFieldInstance(ProcessFieldTemplate $processFieldTemplate)
    {
        if (($model = ProcessFieldInstance::find()->where([
                'instance_id' => $this->id,
                'field_id' => $processFieldTemplate->id,
            ])->one()) !== null
        ) {
            return $model;
        }

        return new ProcessFieldInstance();
    }

    /**
     * @param $id
     * @return array
     */
    public static function getSteps($id)
    {
        $result = [];
        $steps = ProcessInstance::find()->where(['parent' => $id])->asArray()->all();
        foreach ($steps as $value) {
            $result[] = $value['process_id'];
        }
        return $result;
    }

    /**
     * @param $process_id
     * @return ProcessInstance|null
     */
    public static function getProcessId($process_id)
    {
        return self::find()->where(['id' => $process_id])->one();
    }
}


