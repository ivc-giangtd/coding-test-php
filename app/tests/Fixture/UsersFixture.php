<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\Auth\DefaultPasswordHasher;
use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 */
class UsersFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $hasher = new DefaultPasswordHasher();
        $this->records = [
            [
                'id' => 1,
                'email' => 'admin@gmail.com',
                'password' => $hasher->hash('secret'),
                'created_at' => '2024-03-19 08:34:01',
                'updated_at' => '2024-03-19 08:34:01',
            ],
            [
                'id' => 10,
                'email' => 'admin2@gmail.com',
                'password' => $hasher->hash('secret'),
                'created_at' => '2024-03-19 08:34:01',
                'updated_at' => '2024-03-19 08:34:01',
            ],
        ];
        parent::init();
    }
}
