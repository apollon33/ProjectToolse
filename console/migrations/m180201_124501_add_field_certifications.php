<?php

use yii\db\Migration;

/**
 * Class m180201_124501_add_field_certifications
 */
class m180201_124501_add_field_certifications extends Migration
{
    private $updateTableUser = 'user';
    private $skillsEnglishColumn = 'skillsEnglish';
    private $skillsSoftColumn = 'skillsSoft';
    private $certificationUserColumn = 'certificationUser';
    private $skillsFileColumn = 'skills';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if (!in_array($this->updateTableUser, $this->getDb()->schema->tableNames)) {
            return;
        }
        $this->addColumn(
            "{{%$this->updateTableUser}}",
            $this->skillsEnglishColumn,
            $this->string(100)->defaultValue(null)
        );

        $this->addColumn(
            "{{%$this->updateTableUser}}",
            $this->skillsSoftColumn,
            $this->string(100)->defaultValue(null)
        );

        $this->addColumn(
            "{{%$this->updateTableUser}}",
            $this->certificationUserColumn,
            $this->string(100)->defaultValue(null)
        );

        $this->addColumn(
            "{{%$this->updateTableUser}}",
            $this->skillsFileColumn,
            $this->string()->defaultValue(null)
        );

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        if (!in_array($this->updateTableUser, $this->getDb()->schema->tableNames)) {
            return;
        }
        $this->dropColumn(
            "{{%$this->updateTableUser}}",
            $this->skillsEnglishColumn
        );

        $this->dropColumn(
            "{{%$this->updateTableUser}}",
            $this->skillsSoftColumn
        );

        $this->dropColumn(
            "{{%$this->updateTableUser}}",
            $this->certificationUserColumn
        );

        $this->dropColumn(
            "{{%$this->updateTableUser}}",
            $this->skillsFileColumn
        );
    }
}
