<?php

use yii\db\Migration;

class m170401_094108_createUserTable extends Migration
{
    public function up()
    {
        $this->createTable('user',[
            'id' => $this->primaryKey(),
            'objectsid' => $this->string(100)->notNull()->unique(),
            'cn' => $this->string(255),
            'mail' => $this->string(100),
            'dn' => $this->string(255),
            'sn' => $this->string(255),
            'displayname' => $this->string(255),
            'title'=> $this->string(255),
            'objectguid' => $this->string(255),
            'password' => $this->string(60),
            'auth_key' => $this->string(32)->notNull(),
            'thumbnailphoto' => $this->text(),
            'inactive' => $this->boolean(),
            'veryfied' => $this->boolean(),
        ]);
    }

    public function down()
    {
        $this->dropTable('user');
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
