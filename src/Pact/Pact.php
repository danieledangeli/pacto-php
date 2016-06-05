<?php

namespace Erlangb\Phpacto\Pact;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Pact
{
    private $request;
    private $response;
    private $description;
    private $providerState;

    public function __construct(RequestInterface $request, ResponseInterface $response, $description = '', $state = '')
    {
        $this->request = $request;
        $this->response = $response;
        $this->description = $description;
        $this->providerState = $state;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getProviderState()
    {
        return $this->providerState;
    }
}
