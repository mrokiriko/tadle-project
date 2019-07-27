<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m190722_095022_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey()->notNull(),
            'email' => $this->text()->notNull(),
            'password' => $this->text()->notNull(),
            'name' => $this->text(),
            'avatar' => $this->text(),
            'city' => $this->text(),
            'about' => $this->text(),
            'phone' => $this->integer(),
            'date' => $this->timestamp()->notNull(),
            'auth_key' => $this->char([32]),
            'access_token' => $this->char([32]),
        ]);


    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
