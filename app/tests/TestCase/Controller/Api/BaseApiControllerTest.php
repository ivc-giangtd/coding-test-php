<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Api;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\Utility\Security;
use Firebase\JWT\JWT;

/**
 *
 */
class BaseApiControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * @var string
     */
    protected string $userToken;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->userToken = $this->getTokenForLoggedInUser();
    }

    /**
     * @param string $message
     */
    public function assertSuccess(string $message = ''): void
    {
        $this->assertResponseOk($message);
        $this->assertContentType('application/json');
        $this->assertJsonContains('success', true);
    }

    /**
     * @param string $message
     */
    public function assertError(string $message = ''): void
    {
        $this->assertResponseCode(400, $message);
        $this->assertContentType('application/json');
        $this->assertJsonContains('success', false);
    }

    /**
     * @param string $message
     */
    public function assertNotFound(string $message = ''): void
    {
        $this->assertResponseCode(404, $message);
        $this->assertContentType('application/json');
        $this->assertJsonContains('success', false);
    }

    /**
     * @param string $message
     */
    public function assertBadRequest(string $message = ''): void
    {
        $this->assertResponseError($message);
        $this->assertContentType('application/json');
        $this->assertJsonContains('success', false);
        $this->assertJsonContains('message', __('Validation failed'));
    }

    /**
     * @param string $message
     */
    public function assertUnauthorized(string $message = ''): void
    {
        $this->assertResponseCode(401, $message);
        $this->assertContentType('application/json');
        $this->assertJsonContains('success', false);
    }

    /**
     * @param string $message
     */
    public function assertForbidden(string $message = ''): void
    {
        $this->assertResponseCode(403, $message);
        $this->assertContentType('application/json');
        $this->assertJsonContains('success', false);
    }

    /**
     * @param string $expectedKey
     * @param null $expectedValue
     */
    public function assertJsonContains(string $expectedKey, $expectedValue = null): void
    {
        $response = $this->_getBodyAsString();
        $decodedResponse = json_decode($response, true);
        if ($decodedResponse === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \PHPUnit\Framework\Exception('Invalid JSON returned');
        }

        $keys = explode('.', $expectedKey);
        $current = $decodedResponse;

        foreach ($keys as $key) {
            if (!isset($current[$key])) {
                $this->fail("The key '$expectedKey' does not exist in the JSON response.");
            }
            $current = $current[$key];
        }
        if (!is_null($expectedValue)) {
            $this->assertEquals($expectedValue, $current);
        }
    }

    /**
     *
     */
    protected function configRequestWithAuthHeader(): void
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken]
        ]);
    }

    /**
     * @param int $userId
     * @return string
     */
    protected function getTokenForLoggedInUser(int $userId = 1): string
    {
        $payload = [
            'sub' => $userId,
            'exp' => time() + 6800,
        ];
        return JWT::encode($payload, Security::getSalt(), 'HS256');
    }
}
