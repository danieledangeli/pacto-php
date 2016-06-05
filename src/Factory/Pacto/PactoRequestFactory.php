<?php

namespace Erlangb\Phpacto\Factory\Pacto;

use Erlangb\Phpacto\Factory\PactoRequestFactoryInterface;
use Zend\Diactoros\Request;
use Zend\Diactoros\Stream;

class PactoRequestFactory implements PactoRequestFactoryInterface
{
    public function from($requestArray)
    {
        $str = new Stream('php://memory');
        $request = new Request(
            $requestArray['path'],
            $requestArray['method'],
            $str,
            $requestArray['headers']
        );

        return $request;
    }
}
