<?php

use yii\db\Migration;

class m160229_160248_role_init extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_role}}', [
            'id' => $this->primaryKey(),
            'type' => $this->integer(1)->notNull()->defaultValue(0),
            'name' => $this->string(100)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%user_permission}}', [
            'id' => $this->primaryKey(),
            'role_id' => $this->integer()->notNull(),
            'module_id' => $this->integer()->notNull(),
            'type' => $this->integer(1)->unsigned()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->batchInsert('{{%user_role}}', ['id', 'type', 'name'], [
            [1, 1, 'Super Admin'],
            [2, 0, 'Senior Manager'],
            [3, 0, 'Manager'],
            [4, 0, 'Developer'],
        ]);

        $this->batchInsert('{{%user_permission}}', ['id', 'role_id', 'module_id', 'type'], [
            [1, 3, 1, 1],
            [2, 3, 1, 2],
            [3, 3, 1, 3],
            [4, 3, 1, 4],
            [5, 3, 2, 1],
            [6, 3, 2, 2],
            [7, 3, 2, 3],
            [8, 3, 2, 4],
            [9, 3, 3, 1],
            [10, 3, 3, 2],
            [11, 3, 3, 3],
            [12, 3, 3, 4],
            [13, 3, 4, 1],
            [14, 3, 4, 2],
            [15, 3, 4, 3],
            [16, 3, 4, 4],
            [17, 3, 5, 1],
            [18, 3, 5, 2],
            [19, 3, 5, 3],
            [20, 3, 5, 4],
            [21, 3, 6, 1],
            [22, 3, 6, 2],
            [23, 3, 6, 3],
            [24, 3, 6, 4],
            [25, 3, 7, 1],
            [26, 3, 7, 2],
            [27, 3, 7, 3],
            [28, 3, 7, 4],
            [29, 3, 8, 1],
            [30, 3, 8, 2],
            [31, 3, 8, 3],
            [32, 3, 8, 4],
            [33, 3, 9, 1],
            [34, 3, 9, 2],
            [35, 3, 9, 3],
            [36, 3, 9, 4],
            [37, 3, 10, 1],
            [38, 3, 10, 2],
            [39, 3, 10, 3],
            [40, 3, 10, 4],
        ]);

        $this->addForeignKey('fk_user_user_role', '{{%user}}', 'role_id', '{{%user_role}}', 'id', 'RESTRICT', 'CASCADE');

        $this->addForeignKey('fk_user_permission_user_role', '{{%user_permission}}', 'role_id', '{{%user_role}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_user_permission_module', '{{%user_permission}}', 'module_id', '{{%module}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_user_permission_module', '{{%user_permission}}');
        $this->dropForeignKey('fk_user_permission_user_role', '{{%user_permission}}');
        $this->dropForeignKey('fk_user_user_role', '{{%user}}');

        $this->dropTable('{{%user_role}}');
        $this->dropTable('{{%user_permission}}');
    }
}
