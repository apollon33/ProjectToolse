<?php
namespace console\controllers;
use Yii;
use yii\helpers\Url;
use yii\console\Controller;
use modules\currency\models\Currency;
use yii\helpers\Json;

class DaemonController extends Controller{

    public function actionApiparser() {
        $carrencyAll= file_get_contents('https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?json');
        $carrencyAll=Json::decode($carrencyAll);
        foreach ($carrencyAll as $item) {
            if($item['r030']===978||$item['r030']===840)
            {
                $model = new Currency();
                $model->apiCarrency($item);
            }

        }
    }
}
/**
 * Created by PhpStorm.
 * User: dev48
 * Date: 06.06.17
 * Time: 17:15
 */