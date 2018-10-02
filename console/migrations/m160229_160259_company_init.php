<?php

use yii\db\Migration;

class m160229_160259_company_init extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%company}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
        ], $tableOptions);

        $this->batchInsert('{{%company}}', ['id', 'name'], [
            [1, 'ФОП Бежан Н.В.'],
            [2, 'ФОП Котляр А.Г.'],
        ]);
        
        $this->addForeignKey('fk_user_company', '{{%user}}', 'company_id', '{{%company}}', 'id', 'SET NULL', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_user_company', '{{%user}}');

        $this->dropTable('{{%company}}');
    }
}
