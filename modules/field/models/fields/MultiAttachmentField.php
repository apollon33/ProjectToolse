<?php
namespace modules\field\models\fields;

use modules\field\models\{Field, ProcessFieldInstance, ProcessFieldTemplate};
use modules\document\models\Document;
use modules\buildingprocess\models\ProcessInstance;

class MultiAttachmentField extends OneAttachmentField
{
    const FIELD_TYPE = 'MultiAttachment';

    public $attachmentType = 1;

    /**
     * @return array
     */
    public function option()
    {
        return array_merge(parent::option(), [
            'name' => 'Field[' . $this->field_id . ']',
            'multiple' => true
        ]);
    }

}