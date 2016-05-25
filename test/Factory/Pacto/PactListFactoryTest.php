<?php

namespace Erlangb\Phpacto\Factory\Pacto;

use Erlangb\Phpacto\Fixture;

class PactListFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testItShouldParseConsumerContract()
    {
        $pactListFactory = PactListFactory::getPactoListFactory();

        $contract = Fixture::load('hello_world.json');

        $pactList = $pactListFactory->from($contract);

        $this->assertEquals('Animal Service', $pactList->getProvider());
        $this->assertEquals('Zoo App', $pactList->getConsumer());
        $this->assertCount(3, $pactList->all());
    }
}