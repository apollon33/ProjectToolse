<?php
namespace modules\field\models\fields;

use modules\field\models\{Field, ProcessFieldInstance, ProcessFieldTemplate};
use modules\document\models\Document;
use modules\buildingprocess\models\ProcessInstance;

class OneAttachmentField extends LinkField
{
    const FIELD_TYPE = 'Attachment';
    
    public $attachmentType = 3;
    
    /**
     * @var string
     */
    public $type = 'input';

    /**
     * @return null
     */
    public function validation()
    {
        return null;
    }

    /**
     * @return array
     */
    public function option()
    {
        return [
            'name' => 'Field[' . $this->field_id . ']',
            'class' => 'field-name form-control ',
            'id' => $this->name,
            'value' => $this->value,
            'hidden' => false,
            'labelName' => $this->name,
            'type' => 'file',
            'multiple' => false,
        ];
    }

    public function load($value, ProcessInstance $processInstances, ProcessFieldTemplate $processFieldTemplate, $type = false)
    {
        $parentInstanceId = $processInstances->parent;
        $processInstance = ProcessInstance::findOne($parentInstanceId);
        $processId = $processInstance->process_id;
        $processField = ProcessFieldTemplate::find()->where(['process_id' => $processId, 'type_field' => ProcessFieldTemplate::DOCUMENT_FIELD])->one();
        $fieldId = $processField->id;
        $processFieldInstance = ProcessFieldInstance::find()->where(['instance_id' => $parentInstanceId, 'field_id' => $fieldId ])->one();
        $parentDocument = Document::findOne($processFieldInstance->data);
        $attachmentDocument = new Document();
        $attachmentDocument->activeOrig = $attachmentDocument->active;
        $attachmentDocument->name = static::FIELD_TYPE;
        $attachmentDocument->document_type = $type;
        $attachmentDocument->appendTo($parentDocument);
        $id = (string) $attachmentDocument->id;
        $postData = parent::load($id, $processInstances,$processFieldTemplate);
        return $postData;
    }

}