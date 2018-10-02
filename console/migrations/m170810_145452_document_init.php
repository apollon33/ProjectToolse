<?php

use yii\db\Migration;
use modules\module\models\Module;

class m170810_145452_document_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

	    $this->createTable('{{%document}}', [
		    'id' => $this->primaryKey(),
		    'user_id' => $this->integer()->defaultValue(null), // NEW
		    'root' => $this->integer()->defaultValue(null),
		    'lft' => $this->integer()->notNull(),
		    'rgt' => $this->integer()->notNull(),
		    'lvl' => $this->integer(5)->notNull(), // !!!
		    'name' => $this->string(100)->notNull(), // !!!
		    'description' => $this->text()->defaultValue(null), // NEW
		    'file' => $this->string()->defaultValue(null), // NEW
		    'icon' => $this->string()->defaultValue(null),
		    'icon_type' => $this->integer(1)->notNull()->defaultValue(1), // !!!
		    'active' => $this->boolean()->notNull()->defaultValue(true),
		    'selected' => $this->boolean()->notNull()->defaultValue(false),
		    'disabled' => $this->boolean()->notNull()->defaultValue(false),
		    'readonly' => $this->boolean()->notNull()->defaultValue(false),
		    'visible' => $this->boolean()->notNull()->defaultValue(true),
		    'collapsed' => $this->boolean()->notNull()->defaultValue(false),
		    'movable_u' => $this->boolean()->notNull()->defaultValue(true),
		    'movable_d' => $this->boolean()->notNull()->defaultValue(true),
		    'movable_l' => $this->boolean()->notNull()->defaultValue(true),
		    'movable_r' => $this->boolean()->notNull()->defaultValue(true),
		    'removable' => $this->boolean()->notNull()->defaultValue(true),
		    'removable_all' => $this->boolean()->notNull()->defaultValue(false),
		    'created_at' => $this->integer()->defaultValue(null), // NEW
		    'updated_at' => $this->integer()->defaultValue(null), // NEW
	    ], $tableOptions);

	    $this->createIndex('pk_document_root', '{{%document}}', ['root']);
	    $this->createIndex('pk_document_lft', '{{%document}}', ['lft']);
	    $this->createIndex('pk_document_rgt', '{{%document}}', ['rgt']);
	    $this->createIndex('pk_document_lvl', '{{%document}}', ['lvl']);
	    $this->createIndex('pk_document_active', '{{%document}}', ['active']);

	    $this->addForeignKey('fk_document_user', '{{%document}}', 'user_id', '{{%user}}', 'id', 'SET NULL', 'CASCADE');

	    $moduleInvite = Module::findOne(['name' => 'Documents', 'slug' => 'document']);
	    if(empty($moduleInvite)) {
		    $this->batchInsert('{{%module}}', ['parent_id', 'name', 'slug', 'visible', 'sorting'], [
			    [null, 'Documents', 'document', 1, 23],
		    ]);
	    }
    }

    public function safeDown()
    {
	    $moduleInvite = Module::findOne(['name' => 'Documents', 'slug' => 'document']);
	    if(!empty($moduleInvite)) {
		    $moduleInvite->delete();
	    }

	    $this->dropForeignKey('fk_document_user', '{{%document}}');
        $this->dropTable('{{%document}}');
    }
}