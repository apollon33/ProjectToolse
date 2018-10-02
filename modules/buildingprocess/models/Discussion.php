<?php

namespace modules\buildingprocess\models;

use common\models\User;
use yii;
use yii\base\Model;
use common\behaviors\TimestampBehavior;
use modules\document\models\Document;
use yii\helpers\ArrayHelper;
use modules\field\models\ProcessFieldTemplate;
use modules\field\models\ProcessFieldInstance;
use modules\buildingprocess\models\ProcessInstance;

use common\access\AccessManager;


/**
 * Class Discussion
 * @package modules\buildingprocess\models
 */
class Discussion extends Model
{

    const EMAIL = "e-mail";
    const SKYPE = "Skype";
    const WHATSAP = "What'sap";
    const VIDEO_CHAT = "Video chat";
    const LINKED_IN = "linked in";
    const DELETE_BUTTON_VISIBILITY_TIME = 1000;
    const NAME_OF_SELECTED_FILE = "Discussion";
    const REQUIRED_FIELD_VALUE = '1';
    const COUNTER_START = 1;
    public $text;
    public $channel;
    public $created_at;
    public $updated_at;

    /**
     * list of type channel type
     *
     * @return array
     */
    public static function getChannelTypes()
    {
        return [
            '0' => 'not chosen',
            self::EMAIL => Yii::t('yii', 'e-mail'),
            self::SKYPE => Yii::t('yii', 'Skype'),
            self::WHATSAP => Yii::t('yii', "What'sap"),
            self::VIDEO_CHAT => Yii::t('yii', "Video chat"),
            self::LINKED_IN => Yii::t('yii', "linked in"),
        ];
    }

    /**
     * @return string
     */
    public static function generateRandomColor()
    {
        return sprintf('#%02X%02X%02X', rand(0, 255), rand(0, 255), rand(0, 255));
    }

    /**
     * @param $processId
     * @return string
     */
    public static function getParentFolderIdByProcessId($processId)
    {
        $processInstance = ProcessInstance::findOne($processId);
        $processTemplateModel = $processInstance->process;
        $modelProcessField = $processInstance->findModelProcessFieldInstance($processTemplateModel->processFieldTemplateByType);
        $parentFolderId = $modelProcessField->data;
        return $parentFolderId;
    }

    /**
     * @param $modelProcessInstanceField
     * @param $id
     */
    public static function updateProcessInstanceField($modelProcessInstanceField, $id)
    {
        $modelProcessInstanceField->data = (string)$id;
        $modelProcessInstanceField->save();
    }

    /**
     * @param \modules\buildingprocess\models\ProcessInstance $processInstance
     * @return int
     */
    public static function getDiscussionMessagesIdByProcess(ProcessInstance $processInstance)
    {
        $processFieldTemplate = self::getProcessFieldTemplateByProcess($processInstance);
        $processField = $processInstance->findModelProcessFieldInstance($processFieldTemplate);
        return (int)$processField->data;
    }

    /**
     * @param \modules\buildingprocess\models\ProcessInstance $processInstance
     * @return array|null|yii\db\ActiveRecord
     */
    public static function getProcessFieldTemplateByProcess(ProcessInstance $processInstance)
    {
        $processTemplateId = $processInstance->process->id;
        $processFieldTemlate = ProcessFieldTemplate::find()->where(['process_id' => $processTemplateId, 'type_field' => ProcessFieldTemplate::DISCUSSION_FIELD])->one();
        return $processFieldTemlate;
    }

    /**
     * @param $parentFolderId
     * @return int
     */
    public static function  addDiscussionToParentFolder($parentFolderId)
    {
        $messages = [];
        $discussion = new Document();
        $discussion->activeOrig = $discussion->active;
        $discussion->name = Discussion::NAME_OF_SELECTED_FILE;
        $discussion->description = json_encode($messages);
        $discussion->document_type = Document::NODE_TYPE_DISCUSSION;
        $parentFolder = Document::findOne($parentFolderId);
        $discussion->appendTo($parentFolder);
        $discussion->save();
        return $discussion->id;
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['text', 'channel'], 'required'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'text' => Yii::t('app', 'Paste here'),
            'channel' => Yii::t('app', 'Channel'),
        ];
    }

    /**
     * Save $request data in array $messages an aftersave Document
     * @param $modelId
     * @param $messageText
     * @param $channelType
     * @return bool
     */
    public function saveNewDiscussionMessage($modelId, $messageText, $channelType)
    {
        $userId = (int)Yii::$app->user->id;
        $modelUser = User::findOne($userId);
        $userFirstName = (string)$modelUser->first_name;
        $userLastName = (string)$modelUser->last_name;
        $userFullName = $userFirstName . ' ' . $userLastName;
        $modelDocument = Document::findOne($modelId);
        $day = date('l');
        $date = date('d.m.Y H:i:s ');
        $addTime = time();
        $color = $modelUser->color;
        $messages = json_decode($modelDocument->description, true);
        if (count($messages) >= 1) {
            ksort($messages);
            $counter = $messages[count($messages) - 1]['id'] + 1;
        } else {
            $counter = 0;
        }
        $newArrayMessages = [
            'addTime' => $addTime,
            'userId' => $userId,
            'day' => $day,
            'name' => $userFullName,
            'date' => $date,
            'text' => $messageText,
            'channel' => $channelType,
            'color' => $color,
            'id' => $counter,
        ];

        $messages[] = $newArrayMessages;
        $modelDocument->description = json_encode($messages);
        $modelDocument->save();
        return true;
    }

    /**
     * @param $documentId
     * @param $messageId
     * @return bool|mixed
     */
    public function deleteMessage($documentId, $messageId)
    {
        $modelDocument = Document::findOne($documentId);
        $messagesArray = self::getSortedArrayMessages($modelDocument);
        foreach ($messagesArray as $key => $value) {
            if ($messagesArray[$key]['id'] == $messageId) {
                unset($messagesArray[$key]);
                break;
            }
        }
        ArrayHelper::multisort($messagesArray, ['date', 'name'], [SORT_ASC, SORT_DESC]);
        $modelDocument->description = json_encode($messagesArray);
        $modelDocument->save();
        $messagesArray = self::getSortedArrayMessages($modelDocument);
        return $messagesArray;
    }

    /**
     * Sorted $messagesArray by date and name
     * @param Document $modelDocument
     * @return bool|mixed
     */
    public static function getSortedArrayMessages(Document $modelDocument)
    {
        $messagesArray = json_decode($modelDocument->description, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            ArrayHelper::multisort($messagesArray, ['date', 'name'], [SORT_DESC, SORT_ASC]);
            return $messagesArray;
        }
        return $messagesArray = false;
    }
}

