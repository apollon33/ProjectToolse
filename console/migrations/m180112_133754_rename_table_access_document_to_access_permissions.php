<?php

use yii\db\Migration;

/**
 * Class m180112_133754_rename_table_access_document_to_access_permissions
 */
class m180112_133754_rename_table_access_document_to_access_permissions extends Migration
{
    public $oldName = 'access_document';
    private $newName = 'access_permissions';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->renameTable($this->oldName, $this->newName);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        return false;
    }
}
