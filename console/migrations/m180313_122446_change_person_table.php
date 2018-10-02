<?php

use yii\db\Migration;

/**
 * Class m180313_122446_change_person_table
 */
class m180313_122446_change_person_table extends Migration
{
    private $email = 'email';
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropColumn(
            '{{%contact_person}}',
            'e-mail'
        );
        $this->addColumn(
            '{{%contact_person}}',
            $this->email,
            $this->string(64)->defaultValue(null)
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn(
            '{{%contact_person}}',
            $this->email
        );
        $this->addColumn(
            '{{%contact_person}}',
            'e-mail',
            $this->string(64)->defaultValue(null)
        );
    }

}
