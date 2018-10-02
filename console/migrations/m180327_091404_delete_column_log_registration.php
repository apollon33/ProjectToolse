<?php

use yii\db\Migration;

/**
 * Class m180327_091404_delete_column_log_registration
 */
class m180327_091404_delete_column_log_registration extends Migration
{

    private $updateTableLogRegistration = 'log_registration';
    private $deleteColumnCompanyId = 'company_id';

    public function up()
    {
        if (!in_array($this->updateTableLogRegistration, $this->getDb()->schema->tableNames)) {
            return;
        }

        $this->dropForeignKey('fk_log_registration_company', "{{%$this->updateTableLogRegistration}}");
        $this->dropColumn("{{%$this->updateTableLogRegistration}}", $this->deleteColumnCompanyId);
    }

    public function down()
    {
        if (!in_array($this->updateTableLogRegistration, $this->getDb()->schema->tableNames)) {
            return;
        }

        $this->addColumn("{{%$this->updateTableLogRegistration}}", $this->deleteColumnCompanyId, $this->integer()->notNull());
        $this->addForeignKey('fk_log_registration_company', '{{%log_registration}}', 'company_id', '{{%company}}', 'id', 'RESTRICT', 'CASCADE');
    }
}
