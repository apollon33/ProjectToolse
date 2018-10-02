<?php

use yii\db\Migration;

class m160505_112501_log_init extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%log_position}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'position_id' => $this->integer()->notNull(),
            'description' => $this->text()->defaultValue(null),
            'created_at' => $this->integer()->defaultValue(null),
        ], $tableOptions);

        $this->createTable('{{%log_registration}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'registration_id' => $this->integer()->notNull(),
            'company_id' => $this->integer()->notNull(),
            'description' => $this->text()->defaultValue(null),
            'created_at' => $this->integer()->defaultValue(null),
        ], $tableOptions);

        $this->createTable('{{%log_salary}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'salary' => $this->float()->notNull(),
            'currency' => $this->integer()->notNull(),
            'bonus' => $this->float()->notNull(),
            'bonus_currency' => $this->float()->notNull(),
            'reporting_salary' => $this->float()->notNull(),
            'description' => $this->text()->defaultValue(null),
            'created_at' => $this->integer()->defaultValue(null),
        ], $tableOptions);

        $this->addForeignKey('fk_log_position_user', '{{%log_position}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_log_position_position', '{{%log_position}}', 'position_id', '{{%position}}', 'id', 'RESTRICT', 'CASCADE');

        $this->addForeignKey('fk_log_registration_user', '{{%log_registration}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_log_registration_registration', '{{%log_registration}}', 'registration_id', '{{%registration}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk_log_registration_company', '{{%log_registration}}', 'company_id', '{{%company}}', 'id', 'RESTRICT', 'CASCADE');

        $this->addForeignKey('fk_log_salary_company', '{{%log_salary}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_log_salary_company', '{{%log_salary}}');

        $this->dropForeignKey('fk_log_registration_company', '{{%log_registration}}');
        $this->dropForeignKey('fk_log_registration_registration', '{{%log_registration}}');
        $this->dropForeignKey('fk_log_registration_user', '{{%log_registration}}');

        $this->dropForeignKey('fk_log_position_position', '{{%log_position}}');
        $this->dropForeignKey('fk_log_position_user', '{{%log_position}}');

        $this->dropTable('{{%log_salary}}');
        $this->dropTable('{{%log_registration}}');
        $this->dropTable('{{%log_position}}');
    }
}

