<?php

namespace Erlangb\Phpacto\Matcher;

use Erlangb\Phpacto\Diff\Diff;
use Erlangb\Phpacto\Diff\Mismatch;
use Erlangb\Phpacto\Diff\MismatchType;
use Zend\Diactoros\Request;

class HeadersMatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @var  HeadersMatcher */
    private $headersMatcher;

    public function setUp()
    {
        $this->headersMatcher = new HeadersMatcher();
    }
    public function testItShouldReturnsNoDiffIfHeadersAreEquals()
    {
        $messageOne = (new Request())
            ->withHeader('key1', '124')
            ->withHeader('key2', ['ciao', 'ciao']);

        $messageTwo = (new Request())
            ->withHeader('key1', '124')
            ->withHeader('key2', '124')
            ->withHeader('key2', ['ciao', 'ciao']);


        $diff = $this->headersMatcher->match($messageOne, $messageTwo);
        $this->assertCount(0, $diff->getMismatches());
    }

    public function testItShouldReturnsHeaderKeysMismatches()
    {
        $messageOne = (new Request())
            ->withHeader('key1', '124')
            ->withHeader('key2', ['ciao', 'ciao']);

        $messageTwo = (new Request())
            ->withHeader('key1', '124');


        $diff = $this->headersMatcher->match($messageOne, $messageTwo);
        $this->assertCount(1, $diff->getMismatches());
        $this->assertEquals(
            $diff->getMismatches()[0],
            new Mismatch(HeadersMatcher::LOCATION, MismatchType::KEY_NOT_FOUND, ['key2'])
        );
    }

    /**
     * @group it
     */
    public function testItShouldReturnsHeaderValuesMismatches()
    {
        $messageOne = (new Request())
            ->withHeader('key1', 'pippo')
            ->withHeader('key2', ['ciao', 'ciao']);

        $messageTwo = (new Request())
            ->withHeader('key1', '12s4')
            ->withHeader('key2', ['ciao kikko', 'ciao']);


        $diff = $this->headersMatcher->match($messageOne, $messageTwo);
        $this->assertCount(2, $diff->getMismatches());

        $this->assertEquals(
            $diff->getMismatches()[0],
            new Mismatch(HeadersMatcher::LOCATION, MismatchType::UNEQUAL, ['pippo', '12s4'])
        );

        $this->assertEquals(
            $diff->getMismatches()[1],
            new Mismatch(HeadersMatcher::LOCATION, MismatchType::UNEQUAL, ['ciao,ciao', 'ciao kikko,ciao'])
        );
    }
}