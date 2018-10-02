<?php
namespace common\widgets;

use Yii;
use yii\base\Widget;

class Gallery extends Widget
{
    public function init()
    {
        parent::init();

        $view = Yii::$app->getView();
        GalleryAsset::register($view);
    }

    public function run()
    {
        return $this->render('gallery_popup');
    }
}
