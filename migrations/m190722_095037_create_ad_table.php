<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%ad}}`.
 */
class m190722_095037_create_ad_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ad}}', [
            'id' => $this->primaryKey()->notNull(),
            'status' => $this->smallInteger(),
            'category' => $this->text(),
            'price' => $this->integer(),
            'city' => $this->text(),
            'description' => $this->text(),
            'headline' => $this->text(),
            'date' => $this->date(),
            'userId' => $this->integer(),
        ]);

        $this->addForeignKey('userId_fk', '{{%ad}}', 'userId', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%ad}}');
    }
}
