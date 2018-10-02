<?php

use yii\db\Migration;

class m171002_083454_building_process extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%process_template}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(100)->notNull(),
            'type' => $this->string(100)->notNull(),
            'display' => $this->boolean()->notNull()->defaultValue(false),
            'create_folder' => $this->boolean()->notNull()->defaultValue(false),
            'file_url' => $this->integer()->defaultValue(null),
            'description' => $this->text()->defaultValue(null),
            'parent' => $this->integer(11)->defaultValue(null),
            'sorting' => $this->integer(4)->unsigned()->defaultValue(null),
        ], $tableOptions);

        $this->createTable('{{%process_field_template}}', [
            'id' => $this->primaryKey(),
            'process_id' => $this->integer(11)->notNull(),
            'type_field' => $this->string(100)->notNull(),
            'option' => $this->text()->defaultValue(null),
            'name' => $this->string(100)->notNull(),
            'required' => $this->boolean()->notNull()->defaultValue(false),
            'sorting' => $this->integer(4)->unsigned()->defaultValue(null),
        ], $tableOptions);

        $this->createTable('{{%process_instance}}', [
            'id' => $this->primaryKey(),
            'process_id' => $this->integer(11)->notNull(),
            'file_url' => $this->integer()->defaultValue(null),
            'created_at' => $this->integer()->defaultValue(null),
            'updated_at' => $this->integer()->defaultValue(null),
            'owner' => $this->integer()->defaultValue(null),
            'parent' => $this->integer()->defaultValue(null),
        ], $tableOptions);

        $this->createTable('{{%process_field_instance}}', [
            'instance_id' => $this->integer(11)->notNull(),
            'field_id' => $this->integer(11)->notNull(),
            'data' => $this->string(100)->notNull(),
            'PRIMARY KEY (instance_id, field_id)',
        ], $tableOptions);

        $this->addForeignKey('fk_field_process', '{{%process_field_template}}', 'process_id', '{{%process_template}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_instances_process', '{{%process_instance}}', 'process_id', '{{%process_template}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_field_instance', '{{%process_field_instance}}', 'field_id', '{{%process_field_template}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_process_instance', '{{%process_field_instance}}', 'instance_id', '{{%process_instance}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_process_instance_user', '{{%process_instance}}', 'owner', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_process_document', '{{%process_template}}', 'file_url', '{{%document}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_instance_document', '{{%process_instance}}', 'file_url', '{{%document}}', 'id', 'CASCADE', 'CASCADE');

    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_field_process', '{{%process_field_template}}');
        $this->dropForeignKey('fk_instances_process', '{{%process_instance}}');
        $this->dropForeignKey('fk_field_instance', '{{%process_field_instance}}');
        $this->dropForeignKey('fk_process_instance', '{{%process_field_instance}}');
        $this->dropForeignKey('fk_process_instance_user', '{{%process_instance}}');
        $this->dropForeignKey('fk_process_document', '{{%process_template}}');
        $this->dropForeignKey('fk_instance_document', '{{%process_instance}}');
        $this->dropTable('{{%process_field_instance}}');
        $this->dropTable('{{%process_field_template}}');
        $this->dropTable('{{%process_instance}}');
        $this->dropTable('{{%process_template}}');
    }
}
