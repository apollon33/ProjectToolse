<?php

use yii\db\Migration;

/**
 * Handles the creation of table `attachment_entity`.
 */
class m180129_130011_create_attachment_entity_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%attachment_entity}}', [
            'attachment_id' => $this->integer(11)->notNull(),
            'version' => $this->string(14),
            'filename' => $this->string(64),
            'created_at' => $this->integer()->defaultValue(null),
        ], $tableOptions);
        $this->addPrimaryKey('attachment_version', '{{%attachment_entity}}', ['attachment_id', 'version' ]);
        $this->addForeignKey('fk_ent_attach_attachment', '{{%attachment_entity}}', 'attachment_id', '{{%attachment}}', 'id', 'CASCADE', 'CASCADE');

        $data = (new \yii\db\Query())->select(['attachment_id', 'version'])->from('{{%attachment_document}}')->all();
        $this->batchInsert('{{%attachment_entity}}', ['attachment_id', 'version'], $data );

        $this->dropForeignKey('fk_doc_attach_attachment', '{{%attachment_document}}');
        $this->dropForeignKey('fk_doc_attach_document', '{{%attachment_document}}');
        $this->dropTable('{{%attachment_document}}');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%attachment_document}}', [
            'attachment_id' => $this->integer(11)->notNull(),
            'document_id' => $this->integer(11)->notNull(),
            'version' => $this->smallInteger()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('fk_doc_attach_attachment', '{{%attachment_document}}', 'attachment_id', '{{%attachment}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_doc_attach_document', '{{%attachment_document}}', 'document_id', '{{%document}}', 'id', 'CASCADE', 'CASCADE');

        $data = (new \yii\db\Query())->select(['attachment_id', 'document_id', 'version'])->from('{{%attachment_entity}}')->innerJoin('{{%attachment_document}}','attachment_id = attachment_id')->all();
        $this->batchInsert('{{%attachment_document}}', ['attachment_id', 'document_id', 'version'], $data );

        $this->dropForeignKey('fk_ent_attach_attachment', '{{%attachment_entity}}');
        $this->dropTable('{{%attachment_entity}}');
        $tableOptions = null;
    }
}
