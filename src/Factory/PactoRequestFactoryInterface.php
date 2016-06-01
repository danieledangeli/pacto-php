<?php

namespace Erlangb\Phpacto\Factory;

use Psr\Http\Message\RequestInterface;

interface PactoRequestFactoryInterface
{
    /**
     * @param $responseArray
     * @return RequestInterface
     */
    public function from($requestArray);
}