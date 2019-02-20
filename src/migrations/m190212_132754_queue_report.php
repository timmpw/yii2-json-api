<?php

namespace tuyakhov\jsonapi\migrations;

use yii\db\Migration;

/**
 * Class m190212_132754_queue
 */
class m190212_132754_queue_report extends Migration
{

    public $table = 'queue_report';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->table, [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'task_name' => $this->text()->notNull(),
            'status' => $this->string()->notNull(),
            'model' => $this->text()->notNull(),
            'filter' => $this->text(),
            'report_base_url' => $this->text(),
            'report_path' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'completed_at' => $this->integer(),
            'viewed' => $this->boolean()->defaultValue(false),
        ]);

        $this->addForeignKey('user_id_fk',$this->table,'user_id','{{%user}}','id','CASCADE','CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('user_id_fk', $this->table);

        $this->dropTable($this->table);

    }

}
