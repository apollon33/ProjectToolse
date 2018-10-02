<?php

use yii\db\Migration;

/**
 * Migration to add 1:1 field with this user
 */
class m180608_090652_personal_meetings extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('{{%user}}', '[[personal_meeting]]', $this->text()->null());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('{{%user}}', '[[personal_meeting]]');
    }

}
