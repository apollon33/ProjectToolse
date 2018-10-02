<?php

namespace common\behaviors;

use Yii;
use yii\db\ActiveRecord;
use yii\base\Behavior;

class FileBehavior extends Behavior
{
    public $fieldName = 'file';

    public $inputFileName = null;

    public $storage = null;

    /**
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
        ];
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->storage = Yii::$app->fileStorage;
    }

    public function beforeValidate()
    {
        $fileName = $this->storage->getFileName($this->owner, $this->fieldName, $this->inputFileName);
        $this->owner->setAttribute($this->fieldName, $fileName);
    }

    /**
     * @param object $event
     */
    public function beforeSave($event)
    {
        $fileName = $this->storage->getFileName($this->owner, $this->fieldName, $this->inputFileName);
        $this->owner->setAttribute($this->fieldName, $fileName);
    }

    /**
     * @param object $event
     */
    public function afterSave($event)
    {
        $this->storage->saveFile($this->owner, $this->fieldName, $this->inputFileName);
    }

    /**
     * @param object $event
     */
    public function beforeDelete($event)
    {
        $this->storage->deleteFile($this->owner);
    }

    /**
     * @return boolean
     */
    public function deleteFile()
    {
        $this->storage->deleteFile($this->owner);
        $this->owner->setAttribute($this->fieldName, null);
        return $this->owner->save();
    }

    /**
     * @return string
     */
    public function getFileUrl()
    {
        return $this->storage->getFileUrl($this->owner, $this->fieldName);
    }
    
    /**
     * Return attachment file url for web
     * @return string
     */
    public function getAttachmentUrl()
    {
        return $this->storage->getAttachmentUrl($this->owner, $this->fieldName);
    }
    
    /**
     * Return attachment file path on server
     * @return string
     */
    public function getAttachmentPath()
    {
        return $this->storage->getFullFileName($this->owner, $this->fieldName);
    }
}
