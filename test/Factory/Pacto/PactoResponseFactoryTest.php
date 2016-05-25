<?php

namespace Erlangb\Phpacto\Factory\Pacto;

use Erlangb\Phpacto\Fixture;
use Zend\Diactoros\Response\Serializer;

class PactoResponseFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testItShouldReturnsAPsr7Response()
    {
        $factoryRequest = new PactoResponseFactory();

        $response = $factoryRequest->from($this->getResponseArray());

        $this->assertEquals('{"error":"Argh!!!"}', $response->getBody());
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(['Content-Type' => ['application/json;charset=utf-8']], $response->getHeaders());
    }

    private function getResponseArray()
    {
        $content = json_decode(Fixture::load('hello_world.json'), true);
        return $content['interactions'][0]['response'];
    }
}