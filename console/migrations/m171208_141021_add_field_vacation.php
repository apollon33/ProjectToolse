<?php

use yii\db\Migration;

/**
 * Class m171208_141021_add_field_vacation
 */
class m171208_141021_add_field_vacation extends Migration
{

    private $updateTableVacation = 'vacation';
    private $addColumnManagerId ='managerId';
    private $addColumnApprove ='approve';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        if(in_array($this->updateTableVacation, $this->getDb()->schema->tableNames)) {

            $table = Yii::$app->db->schema->getTableSchema("{{%$this->updateTableVacation}}");
            if (empty($table->columns[$this->addColumnManagerId ])) {
                $this->addColumn(
                    "{{%$this->updateTableVacation}}",
                    $this->addColumnManagerId ,
                    $this->integer(11)->defaultValue(null)
                );
            }
            if (empty($table->columns[$this->addColumnApprove ])) {
                $this->addColumn(
                    "{{%$this->updateTableVacation}}",
                    $this->addColumnApprove ,
                    $this->boolean()->defaultValue(null)
                );
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        if(in_array($this->updateTableVacation, $this->getDb()->schema->tableNames)) {
            $table = Yii::$app->db->schema->getTableSchema("{{%$this->updateTableVacation}}");

            if (!empty($table->columns[$this->addColumnManagerId])) {
                $this->dropColumn(
                    "{{%$this->updateTableVacation}}",
                    $this->addColumnManagerId
                );
            }

            if (!empty($table->columns[$this->addColumnApprove])) {
                $this->dropColumn(
                    "{{%$this->updateTableVacation}}",
                    $this->addColumnApprove
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
        echo "m171208_141021_add_field_vacation cannot be reverted.\n";

        return false;
    }
    */
}
