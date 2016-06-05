<?php

namespace Erlangb\Phpacto\Test;

use Erlangb\Phpacto\Matcher\BodyMatcher;
use Erlangb\Phpacto\Matcher\HeadersMatcher;
use Erlangb\Phpacto\Matcher\StatusCodeMatcher;
use Erlangb\Phpacto\Pact\Pact;
use Erlangb\Phpacto\Test\Output\MismatchDiffOutput;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class PactoInstanceTest extends \PHPUnit_Framework_TestCase
{
    private $onSetup;
    private $onTearDown;
    private $makeRequest;
    private $pact;

    public function __construct($name, \Closure $onTearDown, \Closure $onSetUp, \Closure $makeRequest, Pact $p)
    {
        parent::__construct($name);

        $this->onTearDown = $onTearDown;
        $this->onSetup = $onSetUp;
        $this->makeRequest = $makeRequest;
        $this->pact = $p;
    }

    public function setUp()
    {
        parent::setUp();
        call_user_func($this->onSetup, $this->pact->getProviderState());
    }

    public function testItHonorContract()
    {
        $response = call_user_func($this->makeRequest, $this->pact->getRequest());
        $this->assertResponse($this->pact, $response);
    }

    public function tearDown()
    {
        parent::tearDown();
        call_user_func($this->onTearDown, $this->pact->getProviderState());
    }

    public function assertResponse(Pact $p, ResponseInterface $r)
    {
        $headersMatcher = new HeadersMatcher();
        $headersDiff = $headersMatcher->match($p->getResponse(), $r);

        $bodyMatcher = new BodyMatcher();
        $bodyDiff =$bodyMatcher->match($p->getResponse(), $r);

        $statusCodeMatcher = new StatusCodeMatcher();
        $statusCodeDiff = $statusCodeMatcher->match($p->getResponse(), $r);


        $diffs = $headersDiff->merge($bodyDiff, $statusCodeDiff);

        if ($diffs->hasMismatches()) {
            $output = new MismatchDiffOutput(true);

            $this->fail(
                $output->getOutputFor($diffs, $p)
            );
        }
    }
}
