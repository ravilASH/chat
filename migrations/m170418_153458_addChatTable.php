<?php

use yii\db\Migration;

class m170418_153458_addChatTable extends Migration
{
    public function up()
    {
        $this->createTable('chat', [
            'id' => $this->primaryKey(),
            'name' => $this->string(256),
            'creator' => $this->integer(),
            'date_create' => $this->dateTime(),
            'date_update' => $this->dateTime(),
        ]);
    }

    public function down()
    {
        $this->dropTable('chat');
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
