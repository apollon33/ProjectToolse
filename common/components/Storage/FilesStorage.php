<?php

namespace common\components\Storage;

use common\models\Attachment;
use common\models\AttachmentEntity;
use Yii;
use yii\base\Object;
use yii\base\InvalidParamException;
use yii\base\UserException;
use yii\db\Query;
use yii\web\UploadedFile;
use yii\helpers\StringHelper;
use yii\helpers\FileHelper;
use yii\helpers\ArrayHelper;

/**
 * @todo Refactoring required!
 */
class FilesStorage extends FileStorage
{

    /**
     * Storage constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $model
     * @param $fieldName string
     * @param null $inputName
     * @return array|null
     */
    public function getFileNames($model, $fieldName, $inputName = null)
    {
        $files = $this->getFiles($model, $fieldName, $inputName);
        $filesNames = [];
        foreach ($files as $key => $file) {
            $filesNames[] = $file->name;
        }
        return $filesNames;
    }

    /**
     * @param $model
     * @param $fieldName string
     * @param null $inputName
     * @return null|UploadedFile[]
     * @throws \yii\base\InvalidConfigException
     */
    public function getFiles($model, $fieldName, $inputName = null)
    {
        $files = [];
        if (empty($inputName)) {
            $files = UploadedFile::getInstances($model, $fieldName);
        } else {
            $files = UploadedFile::getInstancesByName($inputName);
        }

        if (empty($files)) {
            $this->_errorCode = self::ERROR_NO_FILE;
        } else {
            foreach ($files as $key => $file) {
                $this->fileValidate($file);
            }
        }
        return $files;
    }

    /**
     * @param $model
     * @param $fieldName string
     * @param null $inputName
     * @return bool|UploadedFile[]
     */
    public function saveFiles($model, $fieldName, $inputName = null)
    {
        $files = $this->getFiles($model, $fieldName, $inputName);

        if ($files === null || empty($files) ) {
            return false;
        }
        $filePath = $this->getDirPath($model);
        $this->createDir($filePath);
        $attachmentFiles =  $_FILES['attachmentFiles']['name'];
        $originFilesCount = count($model->attachments);
        $attachmentFiles = array_slice($attachmentFiles, 0, $originFilesCount, true);
        foreach ($files as $key => $file) {
            if (empty($file)) {
                return false;
            }
            $origin_id = null;
            $version = 1;
            if ($model->attachments) {
                $origin_id = array_search($file->name,$attachmentFiles);
                $version = count(AttachmentEntity::find()->where(['attachment_id' => $origin_id ])->all()) + 1;
            }
            if (!$origin_id) {
                $origin_id = null;
                $version = 1;
            }
            if ($origin_id) {
                $file->name = uniqid() . '.' . $file->extension;
                $fullName = $filePath . '/' . $file->name;
                $file->saveAs($fullName, false);
                $attachmentEntity = new AttachmentEntity();
                $attachmentEntity->version = (string) (!empty(Yii::$app->request->post('AttachmentEntity')['version']) ? Yii::$app->request->post('AttachmentEntity')['version'] : $version );
                $attachmentEntity->filename = $file->name;
                $attachmentEntity->created_at = time();
                $attachmentEntity->attachment_id = $origin_id;
                $attachmentEntity->save();
            } else {
                $attachment = new Attachment();
                $attachment->name = $file->name;
                $attachment->save();
                $model->link('attachments', $attachment);
                $file->name = uniqid() . '.' . $file->extension;
                $fullName = $filePath . '/' . $file->name;
                $file->saveAs($fullName, false);
                $attachmentEntity = new AttachmentEntity();
                $attachmentEntity->version =(string) (!empty(Yii::$app->request->post('AttachmentEntity')['version']) ? Yii::$app->request->post('AttachmentEntity')['version'] : $version );
                $attachmentEntity->filename = $file->name;
                $attachmentEntity->created_at = time();
                $attachmentEntity->link('attachment', $attachment);
            }
        }
        return $files;
    }

    /**
     * @param $model
     * @return array
     */
    public function getFullFileNames($model)
    {
        $attachments = $model->attachments;
        $fullFileNames = [];
        foreach ($attachments as $attachment) {
            $fullFileNames[] = $this->getDirPath($model) . '/' . $attachment->img_name ;
        }
        return $fullFileNames;
    }

    /**
     * @param $model
     * @param $fieldName string
     * @param string $prefix
     * @return array|null
     */
    public function getFilesPath($model, $fieldName, $prefix = '')
    {
        $attachments = $model->attachments;
        $path = [];
        foreach ($attachments as $attachment) {
            $path[] =$this->getFileUrl($model, $fieldName, $prefix = '', $attachment->img_name);
        }
        return $path;
    }

    /**
     * @param $model
     * @param $fileId
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function deleteAttachmentFileById($model, $fileId)
    {
        $attachment = Attachment::findOne($fileId);
        $filePath = $this->getDirPath($model) . '/' . $attachment->img_name;
        if (is_file($filePath)) {
            unlink($filePath);
        }
        Yii::$app->db->createCommand()->delete('attachment_document', ['document_id' => $model->id, 'attachment_id' => $fileId  ])->execute();
        $attachment->delete();
    }

    /**
     * @param $fullName string
     * @param $file
     * @param $filePath string
     * @return string
     */
    public function getFileVersion($fullName, $file, $filePath)
    {
        if (file_exists($fullName)) {
            if (preg_match_all('/.\(([0-9]+)\)\.[a-z]+/', $file->name, $matches)) {
                $index = (int)$matches[1][0];
                ++$index;
                $file->name = preg_replace('/(.)\([0-9]+\)(\.[a-z]+)/', "$1(" . $index . ")$2", $file->name);
            } else {
                preg_replace('/(.)(\.[a-z]+)/', '$1(1)$2', $file->name);
                $file->name = preg_replace('/(.)(\.[a-z]+)/', '$1(1)$2', $file->name);
            }
            $fullName = $filePath . '/' . $file->name;
            $fullName =  $this->getFileVersion($fullName, $file, $filePath);
        }
        return $fullName;
    }
   }

