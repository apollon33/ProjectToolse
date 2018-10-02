<?php

namespace common\components;

use Yii;
use yii\base\Object;
use modules\setting\models\Setting;
use modules\vacation\models\Vacation;

class Mailer extends Object
{
    private $adminEmail = 'admin@test.com';
    private $adminName = 'Admin';

    const SUBJECT_SIGNUP = 0;
    const SUBJECT_PASSWORD_RESET = 1;

    public function __construct($config = null)
    {
        $adminEmail = Setting::getValue('admin_email');
        $adminName = Setting::getValue('admin_name');

        if (!empty($adminEmail)) {
            $this->adminEmail = $adminEmail;
        }
        if (!empty($adminName)) {
            $this->adminName = $adminName;
        }

        parent::__construct($config);
    }

    private static function getSubjects()
    {
        return [
            self::SUBJECT_SIGNUP => Yii::t('app', 'Signup Confirmation'),
            self::SUBJECT_PASSWORD_RESET => Yii::t('app', 'Password Reset Link'),
        ];
    }

    public function sendEmail($mail)
    {
        return Yii::$app->mailer->compose()
            ->setTo($this->adminEmail)
            ->setFrom([$mail->sender_email => $mail->sender_name])
            ->setSubject(Yii::$app->name . ': ' . $mail->subject)
            ->setTextBody($mail->body)
            ->send();
    }

    public function sendSignupEmail($user)
    {
        return Yii::$app->mailer->compose(['html' => 'signup-html', 'text' => 'signup-text'], ['user' => $user])
            ->setTo($user->email)
            ->setFrom([$this->adminEmail => $this->adminName])
            ->setSubject($this->getSubject(self::SUBJECT_SIGNUP))
            ->send();
    }

    public function sendPasswordEmail($user)
    {
        $receiverEmail = $user->email;
        $receiverName = $user->username;
        $adminName = $this->adminName;
        $adminEmail = $this->adminEmail;
        $subject = $this->getSubject(self::SUBJECT_PASSWORD_RESET);
        $message = Yii::$app->mailer->compose('passwordReset-html', ['user' => $user])
            ->setFrom(array($adminEmail => $adminName))
            ->setTo(array($receiverEmail => $receiverName))
            ->setSubject("$subject");
        $message->send();
        return $message;
    }

    private function getSubject($subjectCode)
    {
        return Yii::$app->name . ': ' . self::getSubjects()[$subjectCode];
    }

    /**
     * @param Vacation $vacation
     * @return \yii\mail\MessageInterface
     */
    public function sendVacationDeleteMail(Vacation $vacation)
    {
        $isMailSent = Yii::$app->mailer->compose()
            ->setFrom($this->adminEmail)
            ->setTo(Yii::$app->params['temporaryEmail'])
            ->setSubject('Vacation delete')
            ->setTextBody('User' . ' ' . $vacation->user->first_name . ' ' . $vacation->user->last_name . ' ' . 'delete vacation (start' . ' ' . date('Y-m-d', $vacation->start_at) . ' end ' . date('Y-m-d', $vacation->end_at) . ')')
            ->send();
        if (!$isMailSent) {
            Yii::warning('A message about the deletion of a vacation has not been sent');
        }
        return $isMailSent;
    }

    /**
     * @param Vacation $vacation
     * @return \yii\mail\MessageInterface
     */
    public function sendVacationCreateMail(Vacation $vacation)
    {
        $isMailSent = Yii::$app->mailer->compose()
            ->setFrom($this->adminEmail)
            ->setTo(Yii::$app->params['temporaryEmail'])
            ->setSubject('Vacation create')
            ->setTextBody('User' . ' ' . $vacation->user->first_name . ' ' . $vacation->user->last_name . ' ' . 'create vacation (start' . ' ' . date('Y-m-d', $vacation->start_at) . ' end ' . date('Y-m-d', $vacation->end_at) . ')')
            ->send();
        if (!$isMailSent) {
            Yii::warning('A message about the creation of a vacation has not been sent');
        }
        return $isMailSent;
    }

    /**
     * @param Vacation $vacation
     * @return \yii\mail\MessageInterface
     */
    public function sendVacationUpdateMail(Vacation $vacation)
    {
        $isMailSent = Yii::$app->mailer->compose()
            ->setFrom($this->adminEmail)
            ->setTo(Yii::$app->params['temporaryEmail'])
            ->setSubject('Vacation update')
            ->setTextBody('User' . ' ' . $vacation->user->first_name . ' ' . $vacation->user->last_name . ' ' . 'update vacation (start' . ' ' . date('Y-m-d', $vacation->start_at) . ' end ' . date('Y-m-d', $vacation->end_at) . ')')
            ->send();
        if (!$isMailSent) {
            Yii::warning('A message about the updating of a vacation has not been sent');
        }
        return $isMailSent;
    }
}
