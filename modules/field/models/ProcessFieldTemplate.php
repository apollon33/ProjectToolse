<?php

namespace modules\field\models;

use Imagine\Exception\InvalidArgumentException;
use modules\buildingprocess\models\ProcessInstance;
use modules\field\models\fields\Client;
use modules\field\models\fields\ClientField;
use modules\field\models\fields\DisplayAllTabsField;
use modules\field\models\fields\DocumentField;
use modules\field\models\fields\OneAttachmentField;
use modules\field\models\fields\MultiAttachmentField;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\base\InvalidParamException;

use common\behaviors\SortingBehavior;
use common\behaviors\ReadOnlyBehavior;

use modules\field\models\fields\TitleField;
use modules\field\models\fields\TextField;
use modules\field\models\fields\CkeckboxField;
use modules\field\models\fields\SelectField;
use modules\field\models\fields\DateField;
use modules\field\models\fields\LinkField;
use modules\field\models\fields\DiscussionField;


use modules\buildingprocess\models\ProcessTemplate;

/**
 * This is the model class for table "{{%process_field_template}}".
 *
 * @property integer $id
 * @property integer $process_id
 * @property string $type_field
 * @property string $option
 * @property string $name
 * @property string $required
 * @property integer $sorting
 *
 * @property ProcessFieldInstance[] $processFieldInstances
 * @property ProcessTemplate[] $processes
 * @property ProcessTemplate $process
 */
class ProcessFieldTemplate extends ActiveRecord
{
    const REQUIRED_NO = 0;
    const REQUIRED_YES = 1;

    const TITLE_FIELD = 'TitleField';
    const TEXT_FIELD = 'TextField';
    const CKECKBOX_FIELD = 'CkeckboxField';
    const SELECT_FIELD = 'SelectField';
    const DATE_FIELD = 'DateField';
    const LINK_FIELD = 'LinkField';
    const DOCUMENT_FIELD = 'DocumentField';
    const DISCUSSION_FIELD = 'DiscussionField';
    const CLIENT_FIELD = 'Contact_client';
    const CLIENT_FIELD_NAME = 'ClientInfo';
    const CLIENT_CREATE_NO = 0;
    const CLIENT_CREATE_YES = 1;
    const CLIENT = 'Client';
    const DISCUSSION = 'Discussion';
    const DISPLAY_ALL_TABS_FIELD = 'DisplayAllTabs';
    const DISPLAY_ALL_TABS_NAME = 'DisplayAllTabs';
    const ONE_ATTACHMENT_FIELD = 'OneAttachmentField';
    const MULTI_ATTACHMENT_FIELD = 'MultiAttachmentField';

    const DOCUMENT_DEAL = 'Document Deal';
    const NAME_DEAL = 'Name Deal';
    /**
     * list of fields
     *
     * @return array
     */
    public static function getTypeField()
    {
        return [
            self::TITLE_FIELD => Yii::t('yii', 'Title'),
            self::TEXT_FIELD => Yii::t('yii', 'Text'),
            self::CKECKBOX_FIELD => Yii::t('yii', 'Checkbox'),
            self::SELECT_FIELD => Yii::t('yii', 'Select'),
            self::DATE_FIELD => Yii::t('yii', 'Date'),
            self::LINK_FIELD => Yii::t('yii', 'Link to document'),
            self::DOCUMENT_FIELD => Yii::t('yii', 'Document'),
            self::DISCUSSION_FIELD => Yii::t('yii', 'Discussion'),
            self::DISPLAY_ALL_TABS_FIELD => Yii::t('yii', 'Display all tabs'),
            self::ONE_ATTACHMENT_FIELD => Yii::t('yii', 'OneAttachmentField'),
            self::MULTI_ATTACHMENT_FIELD => Yii::t('yii', 'MultiAttachmentField'),
            self::CLIENT_FIELD => Yii::t('yii', 'Client link'),
            self::CLIENT => Yii::t('yii', 'Client'),
        ];
    }

    /**
     * @return array
     */
    public static function getRequired()
    {
        return [
            self::REQUIRED_NO => Yii::t('yii', 'No'),
            self::REQUIRED_YES => Yii::t('yii', 'Yes'),
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
        return '{{%process_field_template}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['process_id', 'type_field', 'name', 'required', 'modify'], 'required'],
            [['process_id', 'sorting'], 'integer'],
            [['option'], 'string'],
            [['type_field'], 'string', 'max' => 100],
            ['name', 'string', 'max' => 255],
            [
                ['process_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ProcessTemplate::className(),
                'targetAttribute' => ['process_id' => 'id'],
            ],
            [['required', 'modify'], 'validationPriorityField'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'process_id' => Yii::t('app', 'Process ID'),
            'type_field' => Yii::t('app', 'Type Field'),
            'option' => Yii::t('app', 'Option'),
            'name' => Yii::t('app', 'Name'),
            'required' => Yii::t('app', 'Required'),
            'modify' => Yii::t('app', 'Modify'),
            'sorting' => Yii::t('app', 'Sorting'),
        ];
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validationPriorityField($attribute, $params)
    {
        if ((boolean)$this->required === true && (boolean)$this->modify === true) {
            return $this->addError($attribute, Yii::t('app', 'Required and Modify cannot be selected simultaneously'));
        }
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcessFieldInstances()
    {
        return $this->hasMany(ProcessFieldInstance::className(), ['field_id' => 'id']);
    }

    /**
     * @param $processId
     */
    public static function createFieldName($processId)
    {
        $processFieldTemplate = new ProcessFieldTemplate();
        if (!$processFieldTemplate->isCheckField($processId, ProcessFieldTemplate::NAME_DEAL)) {
            $processFieldTemplate->process_id = $processId;
            $processFieldTemplate->type_field = ProcessFieldTemplate::TITLE_FIELD;
            $processFieldTemplate->name = Yii::t('app', ProcessFieldTemplate::NAME_DEAL);
            $processFieldTemplate->required = ProcessFieldTemplate::REQUIRED_YES;
            $processFieldTemplate->modify = ProcessFieldTemplate::REQUIRED_NO;
            $processFieldTemplate->save();
        }
    }

    /**
     * @param $processId
     */
    public static function createFieldDocument($processId)
    {
        $processFieldTemplate = new ProcessFieldTemplate();
        if (!$processFieldTemplate->isCheckField($processId, ProcessFieldTemplate::DOCUMENT_DEAL)) {
            $processFieldTemplate->process_id = $processId;
            $processFieldTemplate->type_field = ProcessFieldTemplate::DOCUMENT_FIELD;
            $processFieldTemplate->name = Yii::t('app', ProcessFieldTemplate::DOCUMENT_DEAL);
            $processFieldTemplate->required = ProcessFieldTemplate::REQUIRED_NO;
            $processFieldTemplate->modify = ProcessFieldTemplate::REQUIRED_NO;
            $processFieldTemplate->save();
        }
    }

    /**
     * @param integer $processId
     * @return int
     */
    public static function changeDisplayAllTabsStatus(int $processId)
    {
        $status = ProcessFieldTemplate::find()->select('required')->where(['process_id' => $processId, 'type_field' => self::DISPLAY_ALL_TABS_NAME])->one();
        if (!$status) {
            return 0;
        }
        return (int)$status->required;
    }
    /**
     * @param integer $id
     * @param integer $status
     */
    public static function updateDisplayAllTabs(int $id, int $status)
    {
        $model = ProcessFieldTemplate::find()->where(['process_id' => $id, 'type_field' => self::DISPLAY_ALL_TABS_NAME])->one();
        if ($model === null) {
            $model = new ProcessFieldTemplate();
            $model->process_id = $id;
            $model->type_field = ProcessFieldTemplate::DISPLAY_ALL_TABS_FIELD;
            $model->name = ProcessFieldTemplate::DISPLAY_ALL_TABS_NAME;
            $model->required = $status;
            $model->modify = ProcessFieldTemplate::REQUIRED_NO;
            $model->save();
        } else {
            $model->required = $status;
            $model->modify = ProcessFieldTemplate::REQUIRED_NO;
            $model->save();
        }
    }

    public function isCheckField($processId, $nameField)
    {
        return ProcessFieldTemplate::find()->where(['process_id' => $processId, 'name' => $nameField])->one();
    }



    /**
     * @return bool
     */
    public function getValue()
    {
        $processInstance = $this->processes;
        if (ProcessFieldInstance::findOne(['instance_id' => $processInstance->id, 'field_id' => $this->id])) {

            return true;
        }

        return false;
    }

    /**
     * @param bool $insert
     *
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->getOptionField()) {
                $options = Yii::$app->request->post('OptionField');
                $this->option = $this->option ?? Json::encode($options);
            } else {
                $this->option = null;
            }

            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->getTypeField()[$this->type_field];
    }

    /**
     * @return bool
     */
    public function getOptionField()
    {
        return in_array($this->type_field, [self::CKECKBOX_FIELD, self::SELECT_FIELD, self::CLIENT]);
    }

    /**
     * Deleting unnecessary fields
     * @return array
     */
    public static function getSelectableFieldTypes()
    {
        $fields = ProcessFieldTemplate::getTypeField();
        $type_filds = [];
        foreach ($fields as $k => $field) {
            if ($field == 'Document' || $field == 'Discussion' || $k == self::DISPLAY_ALL_TABS_NAME) {
                continue;
            }
            $type_filds[$k] = $field;
        }
        return $type_filds;
    }

    /**
     * @return array
     */
    public static function selectableFieldTypes() : array
    {
        $listField = self::getSelectableFieldTypes() ;
        unset($listField[self::CLIENT_FIELD]);
        return $listField;
    }

    /**
     * @param integer $id
     * @return bool
     */
    public static function addClientField($id)
    {
        $client = new ProcessFieldTemplate();
        $client->type_field = self::CLIENT_FIELD;
        $client->name = self::CLIENT_FIELD_NAME;
        $client->required = self::REQUIRED_NO;
        $client->modify = self::REQUIRED_NO;
        $client->process_id = $id;
        return $client->save();
    }

    /**
     * @param integer $id
     * @return array|ProcessFieldTemplate|null|ActiveRecord
     */
    public static function checkClient($id)
    {
        return ProcessFieldTemplate::find()->select('required')->where(['process_id' => $id, 'type_field' => self::CLIENT_FIELD])->one();
    }

    /**
     * @param $id
     * @return false|int
     */
    public static function deleteClientField($id)
    {
        $client = ProcessFieldTemplate::find()->where(['process_id' => $id, 'type_field' =>self::CLIENT_FIELD])->one();
        return $client->delete();
    }

    /**
     * @param $process_id
     * @return null|ProcessFieldTemplate
     */
    public static function getProcess($process_id, $client = self::CLIENT_FIELD_NAME)
    {

        return self::find()->where(['process_id' => $process_id, 'name' => $client])->one();
    }
    /**
     * @param $validation
     *
     * @return array
     */
    public function attributer($validation)
    {
        return [
            'id' => $validation['id'],
            'name' => $validation['name'],
            'container' => '.field-' . $validation['id'],
            'input' => '#' . $validation['id'],
            'error' => '.text-danger',
            'validate' => $validation['validation'],
        ];
    }

    /**
     * @param integer $field_id
     * @return bool
     */
    public static function statusClientField($field_id)
    {
        $status = self::find()->where(['id' => $field_id])->one();
        if ($status->type_field == self::CLIENT) {
            return true;
        }
        return false;
    }

    /**
     * @param $field_id
     * @return array|null
     */
    public static function getProcessId($field_id)
    {
        return self::find()->select('process_id')->where(['id' => $field_id])->one();
    }

    /**
     * @param $process_id
     * @return array|null
     */
    public static function getClientField($process_id)
    {
        return self::find()->where(['process_id' => $process_id->parent, 'type_field' =>self::CLIENT_FIELD])->one();
    }

    /**
     * @return Field|CkeckboxField|DateField|LinkField|SelectField|TextField|TitleField
     *
     * @throws InvalidParamException if the class cannot be found
     */

    /**
     * @param null $processInstance
     *
     * @return CkeckboxField|DateField|LinkField|SelectField|TextField|TitleField|DocumentField|DiscussionField
     *
     *   * @throws InvalidParamException if the class cannot be found
     */
    public function createField($processInstance = null)
    {
        switch ($this->type_field) {
            case self::TITLE_FIELD :
                return new TitleField($this, $processInstance);
            case self::TEXT_FIELD :
                return new TextField($this, $processInstance);
            case self::CKECKBOX_FIELD :
                return new CkeckboxField($this, $processInstance);
            case self::SELECT_FIELD :
                return new SelectField($this, $processInstance);
            case self::DATE_FIELD :
                return new DateField($this, $processInstance);
            case self::LINK_FIELD :
                return new LinkField($this, $processInstance);
            case self::DOCUMENT_FIELD :
                return new DocumentField($this, $processInstance);
            case self::DISCUSSION_FIELD :
                return new DiscussionField($this, $processInstance);
            case self::DISPLAY_ALL_TABS_FIELD :
                return new DisplayAllTabsField($this, $processInstance);
            case self::ONE_ATTACHMENT_FIELD :
                return new OneAttachmentField($this, $processInstance);
            case self::MULTI_ATTACHMENT_FIELD :
                return new MultiAttachmentField($this, $processInstance);
            case self::CLIENT_FIELD :
                return new ClientField($this, $processInstance);
            case self::CLIENT :
                return new Client($this, $processInstance);
            default :
                throw new InvalidParamException(Yii::t('app', 'Cant create entity by given field type:'
                    ) . $this->type_field);
        }
    }
}
