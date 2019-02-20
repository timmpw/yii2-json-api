<?php

use yii\db\Migration;

/**
 * Class m190220_084325_queue_report
 */
class m190220_084325_queue_report extends Migration
{
    public $table = '{{%queue_report}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->table, [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'report_name' => $this->text()->notNull(),
            'status' => $this->string()->notNull(),
            'model' => $this->text()->notNull(),
            'filter' => $this->text(),
            'report_base_url' => $this->text(),
            'report_path' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'completed_at' => $this->integer(),
            'viewed' => $this->boolean()->defaultValue(false),
        ]);

        $this->createIndex('{{%queue_report_user_id}}', $this->table, 'user_id', false);
        $this->createIndex('{{%queue_report_status}}', $this->table, 'status', false);
        $this->createIndex('{{%queue_report_viewed}}', $this->table, 'viewed', false);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropTable($this->table);

    }

}