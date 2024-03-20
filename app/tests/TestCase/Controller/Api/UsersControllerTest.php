<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Api;

/**
 * App\Controller\Api\UsersController Test Case
 *
 * @uses \App\Controller\Api\UsersController
 */
class UsersControllerTest extends BaseApiControllerTest
{
    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Users',
    ];


    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\Api\UsersController::login()
     */
    public function testLoginSuccess(): void
    {
        $data = [
            'email' => 'admin@gmail.com',
            'password' => 'secret',
        ];
        $this->post('/login.json', $data);
        $this->assertSuccess();
        $this->assertJsonContains('data.token');
    }

    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\Api\UsersController::login()
     */
    public function testLoginError(): void
    {
        $data = [
            'email' => 'admin@gmail.com',
            'password' => 'secret1',
        ];
        $this->post('/login.json', $data);
        $this->assertError();
        $this->assertJsonContains('message', __('Invalid username or password'));
    }
}
