<?php

use yii\db\Migration;

/**
 * Class m210114_200458_create_category_and_create_news
 */
class m210114_200458_create_category_and_create_news extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('categories', [
            'id' => $this->primaryKey()->notNull(),
            'title' => $this->text()->notNull(),
            'parent_id' => $this->integer(),
        ]);

        $this->addForeignKey('categories__parent_id__fk', 'categories', 'parent_id', 'categories', 'id', 'SET NULL', 'SET NULL');

        $this->createTable('news', [
            'id' => $this->primaryKey()->notNull(),
            'title' => $this->text()->notNull(),
            'category_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('news__category_id__fk', 'news', 'category_id', 'categories', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('news');
        $this->dropTable('categories');

        return true;
    }
}
