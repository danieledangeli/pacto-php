<?php

namespace Erlangb\Phpacto\Matcher;

use Erlangb\Phpacto\Diff\Mismatch;
use Erlangb\Phpacto\Diff\MismatchType;
use Zend\Diactoros\Request;
use Zend\Diactoros\Stream;

class BodyMatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @var  BodyMatcher */
    private $bodyMatcher;

    public function setUp()
    {
        $this->bodyMatcher = new BodyMatcher();
    }

    public function testItShouldReturnsNoDiffIfBodiesAreEquals()
    {
        $stream = new Stream('php://memory', 'w');
        $stream->write(json_encode(['a' => 1, 'b' => 'bella']));

        $messageOne = (new Request())
            ->withBody($stream);

        $streamTwo = new Stream('php://memory', 'w');
        $streamTwo->write(json_encode(['a' => 1, 'b' => 'bella']));

        $messageTwo = (new Request())
            ->withBody($streamTwo);


        $diff = $this->bodyMatcher->match($messageOne, $messageTwo);
        $this->assertCount(0, $diff->getMismatches());
    }

    public function testItShouldReturnsDiffIfBodiesAreNotEquals()
    {
        $stream = new Stream('php://memory', 'w');
        $stream->write(json_encode(['a' => 1, 'b' => 'bella', 'c' => 'u']));

        $messageOne = (new Request())
            ->withBody($stream);

        $streamTwo = new Stream('php://memory', 'w');
        $streamTwo->write(json_encode(['a' => 1, 'b' => 'bella']));

        $messageTwo = (new Request())
            ->withBody($streamTwo);


        $diff = $this->bodyMatcher->match($messageOne, $messageTwo);
        $this->assertCount(1, $diff->getMismatches());

        $this->assertEquals(
            $diff->getMismatches()[0],
            new Mismatch(BodyMatcher::LOCATION, MismatchType::KEY_NOT_FOUND, ['c'])
        );
    }
}