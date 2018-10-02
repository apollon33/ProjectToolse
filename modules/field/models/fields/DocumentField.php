<?php

namespace modules\field\models\fields;

use Yii;

use modules\field\models\Field;

use modules\document\models\Document;
use modules\buildingprocess\models\ProcessInstance;
use modules\field\models\ProcessFieldTemplate;

class DocumentField extends Field
{
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
     * @param $value
     * @param ProcessInstance $processInstances
     * @param ProcessFieldTemplate $processFieldTemplate
     *
     * @return array
     */
    public function load($value, ProcessInstance $processInstances, ProcessFieldTemplate $processFieldTemplate)
    {
        $document = new Document();
        $documentId = null;
        if ($processInstances->process->create_folder) {
            $documentId = $document->createFolderChild($value, $processInstances->process);
        }

        return parent::load((string)$documentId, $processInstances, $processFieldTemplate);
    }

    /**
     * @return array'value' => $this->value,
     */
    public function option()
    {
        return [
            'name' => 'Field[' . $this->field_id . ']',
            'class' => 'field-doc form-control js-parent-document-id',
            'id' =>  str_replace(' ', '-', strtolower($this->name)),
            'hidden' => true,
            'value' => $this->value,
            'labelName' => $this->name
        ];
    }


}
