<?php

namespace common\library;

use Yii;

use yii\web\User;

class UserAccessManager extends User
{
    /**
     * accessChecker configuration error
     * https://github.com/yiisoft/yii2/issues/15462
     * Returns the access checker used for checking access.
     * @return CheckAccessInterface
     */
    protected function getAccessChecker()
    {
        return Yii::$app->get('accessManager');
    }
}