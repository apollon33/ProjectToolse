<?php

namespace modules\buildingprocess\models;

use modules\field\models\Field;
use Yii;
use yii\db\ActiveRecord;
use modules\document\models\Document;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use common\behaviors\SortingBehavior;
use common\behaviors\ReadOnlyBehavior;

use common\components\Validator\PurifierValidator;
use common\access\AccessInterface;

use modules\field\models\ProcessFieldInstance;
use modules\field\models\ProcessFieldTemplate;
use common\access\AccessManager;

/**
 * This is the model class for table "{{%process_template}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $type
 * @property integer $display
 * @property integer $create_folder
 * @property string $description
 * @property integer $parent
 * @property integer $sorting
 *
 * @property ProcessFieldInstance[] $processFieldInstances
 * @property ProcessFieldTemplate[] $fields
 * @property ProcessFieldTemplate[] $processFieldTemplates
 * @property ProcessInstance[] $processInstances
 */
class ProcessTemplate extends ActiveRecord
{
    const DISPLAY_NO = 0;
    const DISPLAY_YES = 1;

    const CREATE_FOLDER_NO = 0;
    const CREATE_FOLDER_YES = 1;

    const DISPLAY_ALL_TABS_NO = 0;
    const DISPLAY_ALL_TABS_YES = 1;

    const FINAL_DEAL = 'Final Deal';

    const SCENARIO_SAVE = 'save';

    public $required = false;
    public $display_all_tabs = 0;

    public static function getDisplayStatuses()
    {
        return [
            self::DISPLAY_NO => Yii::t('yii', 'No'),
            self::DISPLAY_YES => Yii::t('yii', 'Yes'),
        ];
    }
    public static function getDisplayAllTabsStatuses()
    {
        return [
            self::DISPLAY_ALL_TABS_NO => Yii::t('yii', 'No'),
            self::DISPLAY_ALL_TABS_YES => Yii::t('yii', 'Yes'),
        ];
    }

    public static function getCreateFolderStatuses()
    {
        return [
            self::CREATE_FOLDER_NO => Yii::t('yii', 'No'),
            self::CREATE_FOLDER_YES => Yii::t('yii', 'Yes'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            SortingBehavior::className(),
            ReadOnlyBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%process_template}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'type'], 'required'],
            [['display', 'create_folder', 'display_all_tabs', 'parent', 'sorting', 'file_url'], 'integer'],
            [['description'], PurifierValidator::className()],
            [['title', 'type'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'type' => Yii::t('app', 'Type'),
            'display' => Yii::t('app', 'Display'),
            'display_all_tabs' => Yii::t('app', 'Display All Tabs'),
            'create_folder' => Yii::t('app', 'Create Folder'),
            'file_url' => Yii::t('app', 'File Url'),
            'description' => Yii::t('app', 'Description'),
            'parent' => Yii::t('app', 'Parent'),
            'sorting' => Yii::t('app', 'Sorting'),
        ];
    }

    public function scenarios()
    {
        return [
            'default' => [
                'title',
                'display',
                'display_all_tabs',
                'create_folder',
                'file_url',
                'description',
                'parent',
                'sorting',
            ],
            self::SCENARIO_SAVE => [
                'title',
                'type',
                'display',
                'display_all_tabs',
                'create_folder',
                'file_url',
                'description',
                'parent',
                'sorting',
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFields()
    {
        return $this->hasMany(ProcessFieldTemplate::className(), ['process1_id' => 'id'])/*->viaTable('{{%process_field_instance}}', ['field_id' => 'id'])*/;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcessFieldTemplateByType()
    {
        return $this->hasOne(ProcessFieldTemplate::className(), ['process_id' => 'id',])->andWhere(['type_field' => ProcessFieldTemplate::DISCUSSION_FIELD]);
    }

    public function getProcessFieldTemplates()
    {
        return $this->hasMany(ProcessFieldTemplate::className(), ['process_id' => 'id'])->orderBy(['sorting' => SORT_ASC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcessFieldTemplatesFieldLink()
    {
        return $this->hasOne(ProcessFieldTemplate::className(), ['process_id' => 'id'])->where(['name' => ProcessFieldTemplate::DOCUMENT_DEAL]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcessInstances()
    {
        return $this->hasMany(ProcessInstance::className(), ['process_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocument()
    {
        return $this->hasMany(Document::className(), ['id' => 'file_url']);
    }

    /**
     * @return array
     */
    public function getParents()
    {
        return $this->hasMany(self::className(), ['parent' => 'id'])->orderBy('sorting');
    }

    /**
     * Check to create a folder in the document tree
     *
     * @return bool
     */
    public function isCreateFolder()
    {
        return (int)$this->create_folder === self::CREATE_FOLDER_YES;
    }

    /**
     * @param $id
     * @return int
     */
    public function changeStatusDisplayAllTabs($id)
    {
        if (ProcessFieldTemplate::changeDisplayAllTabsStatus($id)) {
            return $this->display_all_tabs = self::DISPLAY_ALL_TABS_YES;
        }
    }

    /**
     * Create a folder in the document tree
     */
    public function createFolderTree()
    {
        $modelDocument = new Document();
        if (self::isCreateFolder()) {
            $this->file_url = $modelDocument->createFolderRoot($this);

        }

    }

    /**
     * @return array
     */
    public static function getList()
    {
        $type = Yii::$app->request->get('type');

        return ArrayHelper::map(ProcessTemplate::find()->where(['type' => $type])->all(), 'id', function ($processBuilding) {
                return $processBuilding->title;
            });
    }

    /**
     * @return array
     */
    public function getListType()
    {
        return ArrayHelper::map(self::find()->select('type')->all(), 'type', function ($processBuilding) {
                return $processBuilding->type;
            });
    }

    /**
     * @return bool
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }
        self::deleteAll('parent = ' . $this->id);
        if (self::isCreateFolder()) {
            Document::deleteDocument($this->file_url);
        }

        return true;
    }

    /**
     * ProcessInstance $actionProcessInstance
     * @return bool
     */
    public static function getLastProcess(ProcessInstance $actionProcessInstance): bool
    {
        $processTemplate = $actionProcessInstance->process;
        $processTemplate = ProcessTemplate::find()
            ->where(['parent' => (int)$processTemplate->parent])
            ->orderBy('sorting desc')->one();
        if ($actionProcessInstance->process_id === $processTemplate->id) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public static function nextLast($actionProcessTemaplates)
    {
        $processTemplate = ProcessTemplate::find()
            ->where(['parent' => (int)$actionProcessTemaplates->parent])
//            ->andWhere(['>', 'sorting', $actionProcessTemaplates->sorting])
            ->orderBy('sorting asc')->all();

        $lastProcessTemplate = $processTemplate[count($processTemplate)-2];
        if ($lastProcessTemplate->id === $actionProcessTemaplates->id) {
            return true;
        }
        return false;
    }

    /**
     * @return array|null|ActiveRecord
     */
    public function getLast()
    {
        return ProcessTemplate::find()
            ->where(['parent' => (int)$this->parent])
            ->andWhere(['>', 'sorting', $this->sorting])
            ->orderBy('sorting asc')->one();
    }


    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        if (empty($this->parent)) {
            ProcessTemplate::createFinalDeal($this->id);
            ProcessFieldTemplate::createFieldName($this->id);
            if (self::isCreateFolder()) {
                ProcessFieldTemplate::createFieldDocument($this->id);
            }
        }
        
    }

    /**
     * creating the final stage
     * @param $parent
     */
    public static function createFinalDeal($parent)
    {
        $finalDeal = ProcessTemplate::find()->where([
            'parent' => $parent,
            'title' => ProcessTemplate::FINAL_DEAL,
        ])->one();
        if (empty($finalDeal)) {
            $processTemplate = new ProcessTemplate();
            $processTemplate->title = ProcessTemplate::FINAL_DEAL;
            $processTemplate->parent = $parent;
            $processTemplate->type = "";
            $processTemplate->save();
        }
        
    }

    /**
     * When adding a new stage, the final stage moves down the list
     * @param $parent
     */
    public static function sortingStageToFinal($parent)
    {
        $buildingProcess = new ProcessTemplate;
        $idList = [];
        $lasIndex = 0;
        $processTemplates = ProcessTemplate::find()->where(['parent' => $parent])->all();
        
        foreach ($processTemplates as $processTemplate) {
            if ($processTemplate->title !== ProcessTemplate::FINAL_DEAL) {
                $idList[] = $processTemplate->id;
            } else {
                $lastIndex = $processTemplate->id;
            }
        }
        $idList[] = $lastIndex;

        $buildingProcess->updateSorting(implode(",", $idList));
    }

    /**
     * @param bool $insert
     *
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            $post = Yii::$app->request->post('ProcessBuilding');
            if (!empty($post['list'])) {
                $this->type = Json::encode($post['list']);
            }
            $this->createFolderTree();

            return true;
        }

        return false;
    }

    /**
     * rename Document
     */
    public function renameDocument()
    {
        if (self::isCreateFolder()) {
            Document::renameDocument($this->file_url, $this->title);
        }
    }

    /**
     * @param integer $status
     */
    public function changeDisplayAllTabs($status)
    {
        return ProcessFieldTemplate::updateDisplayAllTabs($this->id, $status);
    }

    /**
     * @param Field $field
     *
     * @return string
     */
    public function validateClientDeals(Field $field)
    {
        $validateMethodJs = [];
        if ($field->required) {
            $validateMethodJs[] = $field->requiredValidation();
        }

        if ($field->validation()) {
            $validateMethodJs[] = $field->validation();
        }

        return [
            'id' => str_replace(' ', '-', strtolower($field->name)),
            'name' => $field->field_id,
            'validation' => $validateMethodJs,
        ];

    }


    /**
     * @param ProcessInstance $processInstanceF
     *
     * @return array
     */
    public function processField(ProcessInstance $processInstance)
    {
        $processField = [];
        $validationFormStage = [];
        $processFieldTemplates = $this->processFieldTemplates;
        foreach ($processFieldTemplates as $processFieldTemplate) {
            $processField[] = $processFieldTemplate->createField($processInstance);
            $validationFormStage[] = $processFieldTemplate->attributer($this->validateClientDeals($processFieldTemplate->createField()));
        }

        return [
            'processField' => $processField,
            'validationFormStage' => $validationFormStage,
        ];
    }

    public function getNext()
    {
        return ProcessTemplate::find()
            ->where(['parent' => $this->parent])
            ->andWhere(['>', 'sorting', $this->sorting])
            ->orderBy('sorting asc')
            ->one();
    }

    /**
     * @return bool
     */
    public function isModify(): bool
    {
        return (boolean)$this->getProcessFieldTemplates()->where(['modify' => true])->count();
    }

    /**
     * @return ProcessTemplate[]|null
     */
    public static function getAvailableDisplay()
    {
        $query = self::find()->select('type')->distinct();
        $user = Yii::$app->user->getIdentity();


        if (Yii::$app->user->can('buildingprocess.backend.deal:' . AccessManager::VIEW)) {
            return $query
                ->where(['display' => self::DISPLAY_YES])
                ->orderBy(['id' => SORT_ASC])->all();
        }

        return [];
    }

    /**
     * @return string
     */
    public function getUId()
    {
        return 'deal-' . $this->id;
    }

    /**
     * @return null
     */
    public function getUType()
    {
        return self::ACCESS_TYPE;
    }

    /**
     * @return int
     */
    public function getUParent()
    {
        return $this->parent;
    }

    /**
     * @param $id
     * @return array
     */
    public static function getAllSteps($id)
    {
        $result = [];
        $allSteps = ProcessTemplate::find()->where(['parent' => $id])->asArray()->all();
        foreach ($allSteps as $value) {
            $result[] = $value['id'];
        }
        return $result;
    }
}


