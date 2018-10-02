<?php

use yii\db\Migration;
use modules\module\models\Module;

class m170821_091411_event extends Migration
{

    private $createTableName = '{{%event}}';
    private $updateTableNameHoliday = '{{%holiday}}';
    private $updateTableNameHolidayConfig = '{{%holiday_config}}';
    private $addColumnOne ='type';
    private $addColumnTwo ='color';

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->createTableName, [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'type' => $this->integer(1)->notNull(),
            'location' => $this->text()->defaultValue(null),
            'description' => $this->text()->defaultValue(null),
            'created_by' => $this->integer()->notNull(),
            'start_at' => $this->integer()->defaultValue(null),
            'active' => $this->boolean()->defaultValue(true),

        ],$tableOptions);


        $this->addColumn($this->updateTableNameHoliday, $this->addColumnOne, $this->integer()->notNull()->after('name'));
        $this->addColumn($this->updateTableNameHoliday, $this->addColumnTwo, $this->integer()->notNull()->after($this->addColumnOne));
        $this->addColumn($this->updateTableNameHolidayConfig, $this->addColumnOne, $this->integer()->notNull()->after('name'));
        $this->addColumn($this->updateTableNameHolidayConfig, $this->addColumnTwo, $this->integer()->notNull()->after($this->addColumnOne));

        $moduleInvite = Module::findOne(['name' => 'Actioncalendar', 'slug' => 'actioncalendar']);
        if(empty($moduleInvite)) {
            $this->batchInsert('{{%module}}', ['parent_id', 'name', 'slug', 'visible', 'sorting'], [
                [null, 'Actioncalendar', 'actioncalendar', 1, 22],
            ]);
        }

    }



    public function down()
    {
        $this->dropTable($this->createTableName);
    }

}
