<?php


namespace modules\verifyemail\controllers\backend;



use yii\web\Controller;
use Yii;
use yii\base\Exception;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use common\controllers\BaseController;
use yii\base\ExitException;

use hbattat\VerifyEmail;




/**
 * DefaultController implements the CRUD actions for Profile model.
 */
class DefaultController extends BaseController
{

    public $root_email;
    public $email;
/*
    const NOT_DELETE_PROFILE = 5;



    private static function getMessages($options)
    {
        return [
            self::NOT_DELETE_PROFILE => Yii::t('app', 'Cannot delete or update a parent'),
        ];
    }*/

    public function actionIndex()
    {



        if(Yii::$app->request->post()){
            $post = Yii::$app->request->post();
            $email = array();
            $emails = preg_split("/[\s,]+/", $post['email']);
            $flag = array();
            $flagError = array();
            foreach ($emails as $item) {
                $ve = new   VerifyEmail($item, $post['root_email']);
                if($ve->verify()) {
                    $flag = true;
                } else {
                    sleep(1);
                    $ve = new   VerifyEmail($item, $post['root_email']);
                    if($ve->verify()) {
                        $flag = true;
                    } else {
                        $flag = false;
                        if(isset($ve->get_debug()[11])) {
                            if( strripos($ve->get_debug()[11], 'barracudacentral')===true) {
                                $flagError = true;
                            } else {
                                $flagError = false;
                            }
                        }
                    }
                }
                if($flag) {
                    $email[] = [
                        'email' => $item,
                        'message' => $ve->get_debug(),
                        'class' => 'success'
                    ];
                } else {
                    if($flagError) {
                        $email[] = [
                            'email' => $item,
                            'message' => $ve->get_debug(),
                            'class' => 'danger'
                        ];
                    } else {
                        $email[] = [
                            'email' => $item,
                            'message' => $ve->get_debug(),
                            'class' => 'warning'
                        ];
                    }
                }
            }


            return $this->render('index', [
                'root_email' => $this->root_email,
                'emails' => $email,

            ]);
        }else{
            return $this->render('index', [
                'root_email' => $this->root_email,
            ]);
        }
    }

   /* protected function findModel($id)
    {
        if (($model = Profile::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    private function showMessage($messageId, $options = [], $type = 'error')
    {
        Yii::$app->getSession()->setFlash($type, [self::getMessages($options)[$messageId]]);
    }*/
}
