<?php

use yii\db\Migration;

class m170816_144921_add_column_to_position_table extends Migration
{
    private $updateTablePosition = 'position';
    private $updateTableDepartment = 'department';
    private $addColumn ='sorting';

    public function safeUp()
    {

	    if(in_array($this->updateTablePosition, $this->getDb()->schema->tableNames)) {

		    $table = Yii::$app->db->schema->getTableSchema("{{%$this->updateTablePosition}}");
		    if (empty($table->columns[$this->addColumn])) {
			    $this->addColumn(
				    "{{%$this->updateTablePosition}}",
				    $this->addColumn,
				    $this->integer(11)->defaultValue(null)
			    );
		    }
	    }

	    if(in_array($this->updateTableDepartment, $this->getDb()->schema->tableNames)) {

		    $table = Yii::$app->db->schema->getTableSchema("{{%$this->updateTableDepartment}}");
		    if (empty($table->columns[$this->addColumn])) {
			    $this->addColumn(
				    "{{%$this->updateTableDepartment}}",
				    $this->addColumn,
				    $this->integer(11)->defaultValue(null)
			    );
		    }
	    }
    }

    public function safeDown()
    {
	    if(in_array($this->updateTablePosition, $this->getDb()->schema->tableNames)) {
		    $table = Yii::$app->db->schema->getTableSchema("{{%$this->updateTablePosition}}");

		    if (!empty($table->columns[$this->addColumn])) {
			    $this->dropColumn(
				    "{{%$this->updateTablePosition}}",
				    $this->addColumn
			    );
		    }
	    }

	    if(in_array($this->updateTableDepartment, $this->getDb()->schema->tableNames)) {
		    $table = Yii::$app->db->schema->getTableSchema("{{%$this->updateTableDepartment}}");

		    if (!empty($table->columns[$this->addColumn])) {
			    $this->dropColumn(
				    "{{%$this->updateTableDepartment}}",
				    $this->addColumn
			    );
		    }
	    }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170816_144921_add_column_to_position_table cannot be reverted.\n";

        return false;
    }
    */
}
