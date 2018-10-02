<?php

namespace common\behaviors;

use Yii;

class ImageBehavior extends FileBehavior
{
    public $fieldName = 'image';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->storage = Yii::$app->imageStorage;
    }

    /**
     * @return string
     */
    public function getImageUrl($isThumbnail = false)
    {
        return $this->storage->getImageUrl($this->owner, $this->fieldName, $isThumbnail);
    }

    /**
     * @return string
     */
    public function getImageThumbnailUrl()
    {
        return $this->owner->getImageUrl(true);
    }
}
