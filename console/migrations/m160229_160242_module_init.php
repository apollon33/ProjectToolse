<?php

use yii\db\Migration;

class m160229_160242_module_init extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%module}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer()->defaultValue(null),
            'name' => $this->string(100)->notNull(),
            'slug' => $this->string(100)->unique()->notNull(),
            'visible' => $this->boolean()->notNull()->defaultValue(true),
            'sorting' => $this->integer(4)->unsigned()->defaultValue(null),
        ], $tableOptions);

        $this->batchInsert('{{%module}}', ['id', 'parent_id', 'name', 'slug', 'visible', 'sorting'], [
            //[1, null, 'Dashboard', 'dashboard', 1, 1],
            [1, null, 'Users', 'user', 1, 1],
            [2, 1, 'Roles', 'role', 0, 2],
            [3, null, 'Settings', 'setting', 0, 5],
            [4, null, 'Modules', 'module', 0, 6],
            [5, null, 'Imesheet', 'calendar', 1, 9],
            [6, null, 'Holidays', 'holiday', 0, 10],
            [7, null, 'Holiday Config', 'holidayconfig', 0, 11],
            [8, null, 'Clients', 'client', 1, 12],
            [9, null, 'Project', 'project', 1, 16],
            [10, null, 'Profiles', 'profile', 1, 13],
            [11, null, 'Translations', 'i18n', 0, 15],
            [12, null, 'Position', 'position', 0, 17],
            [13, null, 'Registration', 'registration', 0, 18],
            [14, null, 'Payment', 'payment', 1, 19],
            [15, null, 'Vacation', 'vacation', 1, 20],
            [16, null, 'Config', 'config', 1, 20],
            [17, null, 'Activity', 'activity', 1, 8],
            [18, null, 'Currency', 'currency', 1, 21],
            [19, null, 'VerifyEmail', 'verifyemail', 1, 21],
        ]);

        $this->addForeignKey('fk_module_module', '{{%module}}', 'parent_id', '{{%module}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_module_module', '{{%module}}');

        $this->dropTable('{{%module}}');
    }
}
