<?php

declare(strict_types=1);

namespace App\Error;

use Authentication\Authenticator\UnauthenticatedException;
use Cake\Error\ExceptionRenderer;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ApiExceptionRenderer.
 *
 * @package App\Error
 */
class ApiExceptionRenderer extends ExceptionRenderer
{
    /**
     * Render error json view.
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function render(): ResponseInterface
    {
        if($this->_getController()->getRequest()->getParam('prefix') !== 'Api'){
            return parent::render();
        }
        $exception = $this->error;
        if ($exception instanceof UnauthenticatedException) {
            $code = $exception->getCode();
            $message = __('Unauthorized');
        }else{
            $code = $this->getHttpCode($exception);
            $message = $this->_message($exception, $code);
        }
        $method = $this->_method($exception);
        $template = $this->_template($exception, $method, $code);
        $response = $this->controller->getResponse();
        $this->controller->setResponse($response->withStatus($code));
        $this->controller->set([
            'success' => false,
            'message' => $message,
        ]);
        $this->controller->viewBuilder()->setOption('serialize', ['success', 'message']);

        return $this->_outputMessage($template);
    }
}
