<?php

namespace common\components\Storage;

use Yii;
use yii\imagine\Image;
use yii\helpers\FileHelper;

class ImageStorage extends FileStorage
{
    const PREFIX_THUMBNAIL = 'thumbnail-';

    protected $_storageUrl = null;
    protected $_defaultImage = null;

    protected $_width = null;
    protected $_height = null;

    /**
     * Storage constructor.
     */
    public function __construct()
    {
        $this->_storagePath = Yii::getAlias('@image.path');
        $this->_storageUrl = Yii::getAlias('@image.url');
        $this->_defaultImage = Yii::getAlias('@image.default');

        $params = Yii::$app->params;
        $this->_width = $params['image']['width'];
        $this->_height = $params['image']['height'];
    }

    /**
     * @param $entity
     * @param $fieldName
     * @param null $inputName
     * @return bool
     */
    public function saveFile($entity, $fieldName, $inputName = null)
    {
        $file = $this->getFile($entity, $fieldName, $inputName);

        if (empty($file) || !$entity->getAttribute($fieldName)) {
            return false;
        }

        $filePath = $this->getDirPath($entity);
        $fullName = $this->getThumbnailFullFileName($entity, $fieldName);

        $this->createDir($filePath);
        $this->clearDir($filePath);


        if (!$file->saveAs($fullName)) {
            $this->_errorCode = self::ERROR_UPLOAD_ERROR;
            return false;
        }

        if ($file) {
            $this->saveThumbnail($entity, $fieldName);
        }

        return !empty($file);
    }

    /**
     * @param $entity
     * @param $fieldName
     * @param $url
     * @return bool
     */
    public function copyFileFromUrl($entity, $fieldName, $url)
    {
        $file = parent::copyFileFromUrl($entity, $fieldName, $url);

        if ($file) {
            $this->saveThumbnail($entity, $fieldName);
        }

        return !empty($file);
    }

    /**
     * @param $entity
     * @param $fieldName
     * @param bool $isThumbnail
     * @return bool|null|string
     */
    public function getImageUrl($entity, $fieldName, $isThumbnail = false)
    {
        if ($entity->getAttribute($fieldName) == null) {
            return $this->_defaultImage;
        }

        return $this->_storageUrl . '/'
            . $this->getDirName($entity) . '/'
            . $entity->id . '/'
            . ($isThumbnail ? self::PREFIX_THUMBNAIL : '')
            . $entity->getAttribute($fieldName);
    }

    /**
     * @param $fullName
     * @param $thumbnailName
     * @return static
     */
    public function saveThumbnail($entity, $fieldName)
    {
        $fullName = $this->getThumbnailFullFileName($entity, $fieldName);
        $thumbnailName = $this->getThumbnailName($entity, $fieldName);

        if (empty($fullName) || !in_array(FileHelper::getMimeType($fullName), self::$imageMimeTypes)) {
            return false;
        }

        $imagine = new Image();
        $tools = $imagine->getImagine();
        $image = $tools->open($fullName);
        $size = $image->getSize();
        $width = $size->getWidth();
        $height = $size->getHeight();

        if ($width > $height) {
            $this->_height = (int) ($this->_width / $width * $height);
        } else {
            $this->_width = (int) ($this->_height / $height * $width);
        }

        return $imagine::thumbnail($fullName, $this->_width, $this->_height)->save($thumbnailName);
    }

    /**
     * @param $entity
     * @param $fieldName
     * @return string
     */
    private function getThumbnailFullFileName($entity, $fieldName)
    {
        return $this->getDirPath($entity) . '/' . $entity->getAttribute($fieldName);
    }

    /**
     * @param $entity
     * @param $fieldName
     * @return string
     */
    private function getThumbnailName($entity, $fieldName)
    {
        return $this->getDirPath($entity) . '/' . self::PREFIX_THUMBNAIL . $entity->getAttribute($fieldName);
    }
}
