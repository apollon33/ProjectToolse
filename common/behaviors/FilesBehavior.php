<?php

namespace common\behaviors;
use Yii;
use common\components\Storage\FilesStorage;

/**
 * Class FilesBehavior
 * @package common\behaviors
 */
class FilesBehavior extends FileBehavior
{
    /**
     * FilesBehavior constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->storage = Yii::$app->filesStorage;
    }

   
    public function beforeValidate()
    {
        $fileName = $this->storage->getFileNames($this->owner, $this->fieldName, $this->inputFileName);
        if (is_array($fileName)) {
            $this->owner->setAttribute($this->fieldName, null);
        }
    }

    /**
     * @param object $event
     */
    public function beforeSave($event)
    {
        $fileName = $this->storage->getFileNames($this->owner, $this->fieldName, $this->inputFileName);
        if (is_array($fileName)) {
            $this->owner->setAttribute($this->fieldName, null);
        }

    }
    
    /**
     * @param object $event
     */
    public function afterSave($event)
    {
        $this->storage->saveFiles($this->owner, $this->fieldName, $this->inputFileName);
    }

    public function getFilesPath()
    {

        return $this->storage->getFilesPath($this->owner, $this->fieldName);
    }

    /**
     * @param object $event
     */
    public function beforeDelete($event)
    {
        $this->storage->deleteFile($this->owner);
    }

    /**
     * Return attachment file path on server
     * @return string
     */
    public function getAttachmentPath()
    {
        return $this->storage->getFullFileNames($this->owner, $this->fieldName);
    }
    
    /**
     * @param  integer $fileId
     */
    public function deleteAttachmentFileById($fileId)
    {
        $this->storage->deleteAttachmentFileById($this->owner, $fileId);
    }

    /**
     * @return string
     */
    public function getPath ()
    {
        return $this->storage->getDirPath($this->owner);
    }
}

