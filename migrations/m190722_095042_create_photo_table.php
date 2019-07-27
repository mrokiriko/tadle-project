<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%photo}}`.
 */
class m190722_095042_create_photo_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%photo}}', [
            'id' => $this->primaryKey()->notNull(),
            'picture' => $this->text()->notNull(),
            'date' => $this->timestamp()->notNull(),
            'adId' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('adId_fk', '{{%photo}}', 'adId', '{{%ad}}', 'id', 'CASCADE', 'CASCADE');

//        $this->createTable('news', [
//            'id' => Schema::TYPE_PK,
//            'title' => Schema::TYPE_STRING . ' NOT NULL',
//            'content' => Schema::TYPE_TEXT,
//        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%photo}}');
    }
}
