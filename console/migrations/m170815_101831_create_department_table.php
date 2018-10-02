<?php

use yii\db\Migration;
use modules\module\models\Module;
use common\models\User;

/**
 * Handles the creation of table `department`.
 */
class m170815_101831_create_department_table extends Migration
{
	private $createTableName = 'department';
	private $updateTableName = 'position';
	private $addColumnOne ='deep';
	private $addColumnTwo ='department_id';

    /**
     * @inheritdoc
     */
    public function up()
    {
	    $tableOptions = null;
	    if ($this->db->driverName === 'mysql') {
		    $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
	    }

	    if(!in_array($this->createTableName, $this->getDb()->schema->tableNames)) {
		    $this->createTable("{{%$this->createTableName}}", [
			    'id' => $this->primaryKey(),
			    'name' => $this->string(100)->notNull(),
			    'description' => $this->text()->defaultValue(null),
		    ], $tableOptions);

		    $this->batchInsert("{{%$this->createTableName}}", ['id', 'name', 'description'], [
			    [1, 'test-department-1', 'Test Department 1'],
			    [2, 'test-department-2', 'Test Department 2'],
		    ]);
	    }

	    $moduleInvite = Module::findOne(['name' => 'Department', 'slug' => 'department']);
	    if(empty($moduleInvite)) {
		    $this->batchInsert('{{%module}}', ['parent_id', 'name', 'slug', 'visible', 'sorting'], [
			    [null, 'Department', 'department', 0, 22],
		    ]);
	    }

        if(in_array($this->updateTableName, $this->getDb()->schema->tableNames)) {

		    $table = Yii::$app->db->schema->getTableSchema("{{%$this->updateTableName}}");
		    if (empty($table->columns[$this->addColumnOne])) {
			    $this->addColumn(
				    "{{%$this->updateTableName}}",
				    $this->addColumnOne,
				    $this->integer(11)->defaultValue(0)
			    );
		    }
		    if (empty($table->columns[$this->addColumnTwo])) {
			    $this->addColumn(
				    "{{%$this->updateTableName}}",
				    $this->addColumnTwo,
				    $this->integer(11)->defaultValue(null)
			    );

			    $this->addForeignKey('fk_position_department', "{{%$this->updateTableName}}", 'department_id', '{{%department}}', 'id', 'RESTRICT', 'CASCADE');
		    }
	    }
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
	    if(in_array($this->updateTableName, $this->getDb()->schema->tableNames)) {
		    $table = Yii::$app->db->schema->getTableSchema("{{%$this->updateTableName}}");

		    if (!empty($table->columns[$this->addColumnOne])) {
			    $this->dropColumn(
				    "{{%$this->updateTableName}}",
				    $this->addColumnOne
			    );
		    }

		    if (!empty($table->columns[$this->addColumnTwo])) {
			    $this->dropForeignKey('fk_position_department', "{{%$this->updateTableName}}");

			    $this->dropColumn(
				    "{{%$this->updateTableName}}",
				    $this->addColumnTwo
			    );
		    }
	    }

	    $moduleInvite = Module::findOne(['name' => 'Department', 'slug' => 'department']);
	    if(!empty($moduleInvite)) {
		    $moduleInvite->delete();
	    }

	    if(in_array($this->createTableName, $this->getDb()->schema->tableNames)) {
		    $this->dropTable("{{%$this->createTableName}}");
	    }

    }
}
