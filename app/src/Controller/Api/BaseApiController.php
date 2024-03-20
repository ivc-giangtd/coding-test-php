<?php
declare(strict_types=1);


namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Http\Response;

/**
 *
 */
class BaseApiController extends AppController
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Authentication.Authentication');
        $this->loadComponent('Authorization.Authorization');
    }

    /**
     * @param array $data
     * @param string $message
     * @return Response
     */
    protected function renderSuccess(array $data, string $message = ''): Response
    {
        $result = [
            'success' => true,
            'data' => $data,
            'message' => $message,
        ];
        $this->set(compact('result'));
        $this->viewBuilder()->setOption('serialize', 'result');
        return $this->render();
    }

    /**
     * @param string $message
     * @param int $code
     * @return Response
     */
    protected function renderError(string $message, int $code = 400): Response
    {
        $data = [
            'success' => false,
            'message' => $message,
        ];
        $this->set(compact('data'));
        $this->viewBuilder()->setOption('serialize', 'data');
        $this->response = $this->response->withStatus($code);
        return $this->render();
    }

    /**
     * @param array $errors
     * @return Response
     */
    protected function renderBadRequest(array $errors): Response
    {
        foreach ($errors as &$fieldErrors) {
            $fieldErrors = array_values(array_filter($fieldErrors, function ($error) {
                return is_string($error);
            }));
            $fieldErrors = reset($fieldErrors);
        }
        $response = [
            'success' => false,
            'message' => __('Validation failed'),
            'errors' => $errors
        ];
        $this->set(compact('response'));
        $this->viewBuilder()->setOption('serialize', 'response');
        $this->response = $this->response->withStatus(400);
        return $this->render();
    }
}
