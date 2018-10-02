<?php

namespace modules\buildingprocess\services;

use modules\buildingprocess\models\ProcessInstance;
use modules\buildingprocess\models\ProcessTemplate;
use modules\field\models\ProcessFieldTemplate;

/**
 * Class TabsService
 * @package modules\buildingprocess\services
 */
class TabsService
{
    /**
     * @param $process ProcessInstance
     * @return array
     */
    public static function getInactiveSteps(ProcessInstance $process)
    {
        if (ProcessFieldTemplate::changeDisplayAllTabsStatus($process->process_id)) {
            $allSteps = ProcessTemplate::getAllSteps($process->process_id);
            $activeSteps = ProcessInstance::getSteps($process->id);
            $noActiveSteps = array_diff($allSteps, $activeSteps);
            $result = [];
            foreach ($noActiveSteps as $k => $step) {
                if ($k == 0) {
                    continue;
                }
                $activeStep = ProcessTemplate::findOne($step);
                $result[$k]['label'] = $activeStep->title;
            }
            return $result;
        } else {
            return [];
        }
    }
}

