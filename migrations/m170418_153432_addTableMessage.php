<?php

use yii\db\Migration;

class m170418_153432_addTableMessage extends Migration
{
    public function safeUp()
    {
        $this->createTable('message', [
            'id' => $this->primaryKey(),
            'text' => $this->string(2048),
            'chat_id' => $this->integer(),
            'date_create' => $this->dateTime()
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('message');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
