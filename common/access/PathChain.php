<?php

namespace common\access;

use common\controllers\BaseController;
use Yii;

/**
 * Class PathChain
 * Creating a dependency chain for permissions
 *
 * @package common\access
 */
class PathChain
{
    /**
     * create chain of dependencies
     * @return string
     */
    public function create() : string
    {
        $site = explode('-', Yii::$app->id);
        $site = array_pop($site);
        $controller = Yii::$app->controller->id;
        $module = Yii::$app->controller->module->id;
        if (strncmp($module, "app-", 3) === 0) {
            $path = 'core'. '.' . $site . '.' .$controller;
        } else {
            $path = $module . '.' . $site . '.' . Yii::$app->controller->uid();
        }

        return $path;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->create();
    }
}
