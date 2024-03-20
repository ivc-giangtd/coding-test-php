<?php
declare(strict_types=1);

use Cake\Auth\DefaultPasswordHasher;
use Cake\I18n\FrozenTime;
use Migrations\AbstractSeed;

/**
 * Database seed.
 */
class DatabaseSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     *
     * @return void
     */
    public function run(): void
    {
        $hasher = new DefaultPasswordHasher();
        $users = [
            [
                'id' => 1,
                'email' => 'admin@gmail.com',
                'password' => $hasher->hash('secret'),
                'created_at' => FrozenTime::now()->toDateTimeString(),
                'updated_at' => FrozenTime::now()->toDateTimeString(),
            ],
            [
                'id' => 2,
                'email' => 'admin1@gmail.com',
                'password' => $hasher->hash('secret'),
                'created_at' => FrozenTime::now()->toDateTimeString(),
                'updated_at' => FrozenTime::now()->toDateTimeString(),
            ]
        ];

        $usersTable = $this->table('users');
        $usersTable->insert($users)->save();
    }
}
