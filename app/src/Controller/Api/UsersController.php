<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\Utility\Security;
use Firebase\JWT\JWT;

/**
 * Users Controller
 */
class UsersController extends BaseApiController
{
    public function beforeFilter(EventInterface $event)
    {
        $this->Authentication->allowUnauthenticated(['login']);
        parent::beforeFilter($event);
    }

    public function login(): Response
    {
        $this->Authorization->skipAuthorization();
        $result = $this->Authentication->getResult();
        if (!$result->isValid()) {
            return $this->renderError(__('Invalid username or password'));
        }

        $user = $result->getData();
        $payload = [
            'sub' => $user->id,
            'exp' => time() + 6800, // add 6800s
        ];

        $json = [
            'token' => JWT::encode($payload, Security::getSalt(), 'HS256'),
        ];

        return $this->renderSuccess($json);
    }
}
