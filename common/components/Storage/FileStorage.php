<?php

namespace common\components\Storage;

use Yii;
use yii\base\Object;
use yii\base\InvalidParamException;
use yii\web\UploadedFile;
use yii\helpers\StringHelper;
use yii\helpers\FileHelper;


/**
 * @todo Refactoring required!
 */
class FileStorage extends Object
{
    const DIR_USER = 'user';
    const DIR_MEDIA = 'media';
    const DIR_PRODUCT = 'product';
    const DIR_QUOTE = 'quote';
    const DIR_SLIDER = 'slider';
    const DIR_LOTTERY = 'lottery';
    const DIR_CAMPAIGN = 'campaign';
    const DIR_DOCUMENT = 'document';

    const ERROR_NO_FILE = 1;
    const ERROR_EMPTY_FILE = 2;
    const ERROR_WRONG_FORMAT = 3;
    const ERROR_UPLOAD_ERROR = 4;
    public static $imageMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    public static $videoMimeTypes = ['video/quicktime', 'video/mp4', 'video/webm', 'video/ogg', 'video/x-msvideo'];
    public static $textMimeTypes = [
        'application/pdf',
        'application/zip',
        'application/msword',
        'application/vnd.oasis.opendocument.text',
        'application/vnd.ms-office',
        'application/vnd.oasis.opendocument.text',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // For docx
        'text/plain',
    ];
    protected $directories = [
        'User' => self::DIR_USER,
        'Media' => self::DIR_MEDIA,
        'Product' => self::DIR_PRODUCT,
        'Quote' => self::DIR_QUOTE,
        'Slider' => self::DIR_SLIDER,
        'Document' => self::DIR_DOCUMENT,
        'Attachment' => self::DIR_DOCUMENT,
    ];
    protected $_storagePath = null;

    protected $_errorCode = null;

    /**
     * Storage constructor.
     */
    public function __construct()
    {
        try {
            $this->_storagePath = Yii::getAlias('@file.secure');
            if ((bool)$this->_storagePath === false) {
                throw new InvalidParamException("Empty secure file storage path");
            }
        } catch (InvalidParamException $ex) {
            Yii::error($ex->getMessage());
            throw new InvalidParamException("You should specify @file.secure path in common configuration");
        }
    }

    /**
     * @return array
     */
    protected static function getMessages()
    {
        return [
            self::ERROR_NO_FILE => Yii::t('app', 'No file uploaded.'),
            self::ERROR_EMPTY_FILE => Yii::t('app', 'The file is empty.'),
            self::ERROR_WRONG_FORMAT => Yii::t('app', 'Wrong file format.'), //  Allowed formats are jpg, png and gif.
            self::ERROR_UPLOAD_ERROR => Yii::t('app', 'File uploading error. No writing/modifying file permission.'),
        ];
    }

    /**
     * @param $entity
     * @param $fieldName
     * @param null $inputName
     * @param $file
     * @return null|string
     */
    public function getFileName($entity, $fieldName, $inputName = null, $file = null)
    {
        if (empty($entity->file)) {
            $file = $this->getFile($entity, $fieldName, $inputName);
        }
        $fileName = null;
        if (!empty($file)) {
            $fileName = $file->name;
        } elseif (!$entity->isNewRecord && Yii::$app->controller->action->id != 'delete-image') {
            if ($entity->getAttribute($fieldName) != null) {
                $fileName = $entity->getAttribute($fieldName);
            } else {
                $this->deleteFile($entity);
            }
        }
        return $fileName;
    }

    /**
     * @param $entity
     * @param $fieldName
     * @param null $inputName
     * @return bool|null|UploadedFile
     */
    public function saveFile($entity, $fieldName, $inputName = null)
    {
        $file = $this->getFile($entity, $fieldName, $inputName);

        if (empty($file) || !$entity->getAttribute($fieldName)) {
            return false;
        }

        $filePath = $this->getDirPath($entity, $fieldName);
        $fullName = $this->getFullFileName($entity, $fieldName);

        $this->createDir($filePath);
        $this->clearDir($filePath);


        if (!$file->saveAs($fullName)) {
            $this->_errorCode = self::ERROR_UPLOAD_ERROR;
            return false;
        }

        return $file;
    }

    /**
     * @param $entity
     * @param $fieldName
     * @param null $inputName
     * @return null|UploadedFile
     * @throws \yii\base\InvalidConfigException
     */
    public function getFile($entity, $fieldName, $inputName = null)
    {
        if (empty($inputName) && empty($file)) {
            $file = UploadedFile::getInstance($entity, $fieldName);
        } elseif (!empty($inputName)) {
            $file = UploadedFile::getInstanceByName($inputName);
        }
        if ($this->fileValidate($file)) {
            return $file;
        }

        return null;
    }

    /**
     * @param $entity
     */
    public function deleteFile($entity)
    {
        $filePath = $this->getDirPath($entity);
        $this->clearDir($filePath);
        $this->deleteDir($filePath);
    }

    /**
     * @param $entity
     * @return string
     */
    public function getDirPath($entity, $fieldName = false)
    {
        if($fieldName) {
            return $this->_storagePath . '/' . $this->getDirName($entity) . '/' . $fieldName . '/' . $entity->id;
        }
        return $this->_storagePath . '/' . $this->getDirName($entity) . '/' . $entity->id;
    }

    /**
     * @param $entity
     * @return mixed
     */
    protected function getDirName($entity)
    {
        $entityClass = StringHelper::basename(get_class($entity));
        return $this->directories[$entityClass];
    }

    /**
     * @param string $dir
     */
    protected function clearDir($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        array_map(function ($fileName) use ($dir) {
            if ($fileName != '.' && $fileName != '..') {
                unlink($dir . '/' . $fileName);
            }
        }, scandir($dir));
    }

    /**
     * @param $dir
     */
    protected function deleteDir($dir)
    {
        if (is_dir($dir)) {
            rmdir($dir);
        }
    }
    
    /**
     * @param $entity
     * @param $fieldName
     * @return string
     */
    public function getFullFileName($entity, $fieldName)
    {
        return $this->getDirPath($entity, $fieldName) . '/' . $entity->getAttribute($fieldName);
    }

    /**
     * @param $dir
     */
    protected function createDir($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    /**
     * @param $entity
     * @param $fieldName
     * @param $url
     * @return bool
     */
    public function copyFileFromUrl($entity, $fieldName, $url)
    {
        if (empty($url)) {
            return false;
        }

        $extension = pathinfo($url)['extension'];
        if (strpos($extension, '?') !== false) {
            $extension = substr($extension, 0, strpos($extension, '?'));
        }

        $time = new \DateTime('now');
        $fileName = $time->getTimestamp() . '.' . $extension;

        $entity->setAttribute($fieldName, $fileName);

        $filePath = $this->getDirPath($entity, $fieldName);
        $fullName = $this->getFullFileName($entity, $fieldName);

        $this->createDir($filePath);
        $this->clearDir($filePath);

        $fileContent = file_get_contents($url);
        if (!file_put_contents($fullName, $fileContent)) {
            return false;
        }

        return true;
    }

    /**
     * @param $entity
     * @param $fieldName
     * @param string $prefix
     * @param null $imageName
     * @return null|string
     */
    public function getFileUrl($entity, $fieldName, $prefix = '', $imageName = null)
    {
        if ($entity->getAttribute($fieldName) == null) {
            return null;
        } elseif ($imageName == null ) {
            $imageName = $entity->getAttribute($fieldName);
        }
        return 'storage/file'
        . $this->getDirName($entity) . '/'
        . $entity->id . '/'
        . $prefix
        . $imageName;
    }

    /**
     * Return link to download attachment
     * @param $entity
     * @param string $fieldName
     * @return NULL|string
     */
    public function getAttachmentUrl($entity, $fieldName)
    {
        if ($entity->getAttribute($fieldName) == null) {
            return null;
        }

        $route = Yii::$app->urlManager->parseRequest(Yii::$app->request);
        if ($route === false) {
            return '#';
        }
        $path = explode('/', $route[0]);
        array_splice($path, -1, 1, 'attachment');
        if (count($entity->attachments) >= 1) {
            $pathToImages = array();
            foreach ($entity->attachments as $key => $attachment) {
                $pathToImages[] = Yii::$app->urlManager->createUrl([implode('/', $path), 'id' => $entity->id,'imageKey' => $key ]);
            }
            return $pathToImages;
        }
        return Yii::$app->urlManager->createUrl([implode('/', $path), 'id' => $entity->id]);
    }

    /**
     * @param $file
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function fileValidate($file)
    {
        if ($file == null) {
            $this->_errorCode = self::ERROR_NO_FILE;
            return false;
        } elseif ($file->size == 0) {
            $this->_errorCode = self::ERROR_EMPTY_FILE;
            return false;
        } elseif (!in_array(FileHelper::getMimeType($file->tempName), array_merge(self::$imageMimeTypes, self::$videoMimeTypes, self::$textMimeTypes))) {
            $this->_errorCode = self::ERROR_WRONG_FORMAT;
            return false;
        } elseif ($file->tempName == null) {
            $this->_errorCode = self::ERROR_NO_FILE;
            return false;
        }
        return true;
    }
}
