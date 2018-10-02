<?php

use yii\db\Migration;

/**
 * Class m180124_121053_add_field_eng_name_user
 */
class m180124_121053_add_field_eng_name_user extends Migration
{

    private $updateTableUser = 'user';
    private $firstnameColumn = 'first_name_en';
    private $lastnameColumn = 'last_name_en';
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if (!in_array($this->updateTableUser, $this->getDb()->schema->tableNames)) {
            return;
        }
        $table = Yii::$app->db->schema->getTableSchema("{{%$this->updateTableUser}}");
        if (empty($table->columns[$this->firstnameColumn])) {
            $this->addColumn(
                "{{%$this->updateTableUser}}",
                $this->firstnameColumn,
                $this->string(100)->defaultValue(null)
            );
        }
        if (empty($table->columns[$this->lastnameColumn])) {
            $this->addColumn(
                "{{%$this->updateTableUser}}",
                $this->lastnameColumn,
                $this->string(100)->defaultValue(null)
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        if (!in_array($this->updateTableUser, $this->getDb()->schema->tableNames)) {
            return;
        }
        $table = Yii::$app->db->schema->getTableSchema("{{%$this->updateTableUser}}");
        if (!empty($table->columns[$this->firstnameColumn])) {
            $this->dropColumn(
                "{{%$this->updateTableUser}}",
                $this->firstnameColumn
            );
        }
        if (!empty($table->columns[$this->lastnameColumn])) {
            $this->dropColumn(
                "{{%$this->updateTableUser}}",
                $this->lastnameColumn
            );
        }
    }

}
