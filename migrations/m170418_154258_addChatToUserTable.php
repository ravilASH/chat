<?php

use yii\db\Migration;

class m170418_154258_addChatToUserTable extends Migration
{
    public function safeUp()
    {
        $this->createTable('chat_to_user',[
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'chat_id' => $this->integer(),
        ]);
        $this->addForeignKey('chat_to_user_user_id_fk', 'chat_to_user', 'user_id', 'user', 'id', 'RESTRICT');
        $this->addForeignKey('chat_to_user_chat_id_fk', 'chat_to_user', 'chat_id', 'chat', 'id', 'RESTRICT');
    }

    public function safeDown()
    {
        $this->dropTable('chat_to_user');
        $this->dropForeignKey('chat_to_user_user_id_fk', 'chat_to_user');
        $this->dropForeignKey('chat_to_user_chat_id_fk', 'chat_to_user');
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
