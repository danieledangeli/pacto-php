<?php

namespace Erlangb\Phpacto\Factory;

use Psr\Http\Message\RequestInterface;

interface PactoRequestFactoryInterface
{
    /**
     * @param $requestArray
     * @return RequestInterface
     */
    public function from($requestArray);
}
