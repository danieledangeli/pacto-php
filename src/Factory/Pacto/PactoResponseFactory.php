<?php

namespace Erlangb\Phpacto\Factory\Pacto;

use Erlangb\Phpacto\Factory\PactoResponseFactoryInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class PactoResponseFactory implements PactoResponseFactoryInterface
{

    public function from($responseArray)
    {
        $body = isset($responseArray['body']) ? $responseArray['body'] : '';

        $stream = new Stream('php://memory', 'w');
        $stream->write(json_encode($body));

        $response = new Response(
            $stream,
            $responseArray['status']
        );

        if(isset($responseArray['headers'])) {
            foreach($responseArray['headers'] as $key => $value) {
                $response = $response->withAddedHeader($key, $value);
            }

        }

        return $response;
    }
}