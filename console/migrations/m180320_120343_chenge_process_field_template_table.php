<?php

use yii\db\Migration;
use yii\db\Expression;
use modules\field\models\ProcessFieldTemplate;


/**
 * Class m180320_120343_chenge_process_field_template_table
 */
class m180320_120343_chenge_process_field_template_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $query = \Yii::$app->db->createCommand()
            ->update(
                '{{%process_field_template}}',
                [
                    'option' => new Expression("CONCAT('[\"', [[name]], '\"]')"),
                    'name' => new Expression("CONCAT('Client-', [[name]])")
                ],
                [
                    'type_field' => ProcessFieldTemplate::CLIENT,
                    'option' => null
                ]
            );
        $query->execute();
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        return true;
    }
}
