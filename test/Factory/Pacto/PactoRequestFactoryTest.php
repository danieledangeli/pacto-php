<?php

namespace Erlangb\Phpacto\Factory\Pacto;

use Erlangb\Phpacto\Fixture;

class PactoRequestFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testItShouldReturnsAPsr7Request()
    {
        $factoryRequest = new PactoRequestFactory();

        $request = $factoryRequest->from($this->getRequestArray());

        $this->assertEquals('get', $request->getMethod());
        $this->assertEquals('/alligators/Mary', $request->getUri());
    }

    private function getRequestArray()
    {
        $content = json_decode(Fixture::load('hello_world.json'), true);
        return $content['interactions'][0]['request'];
    }
}