<?php

use yii\db\Migration;

/**
 * Class m180306_112126_contact_person_table
 */
class m180306_112126_contact_person_table extends Migration
{
    private $company_id = 'company_id';
    private $address = 'payer_address';
    private $phone = 'phone';
    private $email = 'e-mail';
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%contact_person}}', [
            'id' => $this->primaryKey(),
            $this->company_id => $this->integer(11)->defaultValue(null),
            $this->address => $this->string(100)->defaultValue(null),
            $this->phone => $this->string(100)->defaultValue(null),
            $this->email => $this->string(100)->defaultValue(null),
        ]
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%contact_person}}');
    }
}
