<?php

use yii\db\Migration;

/**
 * Class m190220_081542_queue_report
 */
class m190220_081542_queue_report extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190220_081542_queue_report cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190220_081542_queue_report cannot be reverted.\n";

        return false;
    }
    */
}
