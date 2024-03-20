<?php
use Migrations\AbstractMigration;

class CreateLikes extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('likes');
        $table->addColumn('article_id', 'integer')
              ->addColumn('user_id', 'integer')
              ->addColumn('created_at', 'datetime')
              ->addIndex(['article_id', 'user_id'], ['name' => 'article_id_user_id', 'unique' => true])
              ->create();
    }
}
