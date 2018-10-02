<?php

namespace common\behaviors;

use Yii;
use yii\base\Behavior;

class FilterBehavior extends Behavior
{
    public $exceptionFields = null;

    /**
     * @param $data
     * @param null $formName
     * @return mixed
     */
    public function loadAttributes($data)
    {
        $entityClass = $this->owner->formName();

        $params = null;
        $session = Yii::$app->session;
        $filterClass = $session->get('filterClass');
        $filterDate = $session->get('filterDate');

        if (!empty($this->exceptionFields)) {
            foreach ($this->exceptionFields as $exception) {
                unset($filterDate[$exception]);
            }
        }

        if (!empty($filterClass) && !empty($filterDate) && count($filterDate) > 0 && $entityClass == $filterClass) {
            $params = array($filterClass => $filterDate);
        }

        if ($this->owner->load($data)) {
            $session->set('filterClass', $entityClass);
            $session->set('filterDate', $data[$entityClass]);
        } elseif (empty($params) || !$this->owner->load($params)) {
            self::clearFilters();
            return false;
        }

        return true;
    }

    public static function clearFilters()
    {
        Yii::$app->session->remove('filterClass');
        Yii::$app->session->remove('filterDate');
    }
}
