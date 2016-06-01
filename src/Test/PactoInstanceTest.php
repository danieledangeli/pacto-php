<?php

namespace Erlangb\Phpacto\Test;


use Erlangb\Phpacto\Consumer\Pact;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class PactoInstanceTest extends \PHPUnit_Framework_TestCase
{
    private $onSetup;
    private $onTearDown;
    private $makeRequest;
    private $pact;
    private $strict;

    public function __construct($name, \Closure $onTearDown, \Closure $onSetUp, \Closure $makeRequest, Pact $p, $strict = false)
    {
        parent::__construct($name);
        $this->onTearDown = $onTearDown;
        $this->onSetup = $onSetUp;
        $this->makeRequest = $makeRequest;
        $this->pact = $p;
        $this->strict = $strict;
    }

    public function setUp()
    {
        parent::setUp();
        call_user_func($this->onSetup, $this->pact->getProviderState());
    }

    public function testItHonorContract()
    {
        $response = call_user_func($this->makeRequest, $this->pact->getRequest());
        $this->assertResponse($this->pact, $response, $this->strict);
    }

    public function tearDown()
    {
        parent::tearDown();
        call_user_func($this->onTearDown, $this->pact->getProviderState());
    }

    public function assertResponse(Pact $p, ResponseInterface $r, $strict = true)
    {
        if($strict) {
            $this->assertStrictResponse($p, $r);
        } else {
            $this->assertSameResponse($p, $r);
        }
    }

    private function assertStrictResponse(Pact $p, ResponseInterface $response)
    {
        $this->assertSameResponse($p, $response);
        $this->compareBodyResponse($p, $p->getResponse()->getBody(), $response->getBody());
    }

    private function assertSameResponse(Pact $p, ResponseInterface $response)
    {
        $expectedResponse = $p->getResponse();

        $this->compareStatusCodes($p, $expectedResponse->getStatusCode(), $response->getStatusCode());
        $this->compareHeaders($p, $expectedResponse->getHeaders(), $response->getHeaders());
        $this->compareSameBodyResponse($p, $expectedResponse->getBody(), $response->getBody());
    }

    private function compareStatusCodes(Pact $p, $expected, $statusCode)
    {
        $this->assertEquals($expected, $statusCode, sprintf("Test %s \n, Status Code Expectation Failed", $p->getDescription()));
    }

    private function compareHeaders(Pact $p, $expectedHeaders, $headers)
    {
        $headers = array_map(function($h) { return strtolower($h[0]);}, $headers);

        foreach($expectedHeaders as $expectedKey => $expectedValue) {
            $this->assertArrayHasKey(
                strtolower($expectedKey),
                $headers,
                sprintf("Test %s \n, Missed header \"%s\", Got Headers: \n %s", $p->getDescription(), $expectedKey, json_encode($headers))
            );


            $this->assertEquals(
                strtolower($expectedValue[0]),
                strtolower($headers[strtolower($expectedKey)]),
                sprintf("Test %s \n, Headers content mismatch: Expected \"%s\" Got: %s", $p->getDescription(), $expectedValue[0], $headers[strtolower($expectedKey)])
            );
        }
    }

    private function compareBodyResponse(Pact $p, StreamInterface $expected, StreamInterface $body)
    {
        $expected->rewind();
        $body->rewind();

        $expectedBody = $expected->getContents();
        $body = $body->getContents();

        $this->assertEquals(
            $expectedBody,
            $body,
            sprintf("Test: %s \n,Body mismatch Expected: \"%s\", Got: \"%s\"", $p->getDescription(), $expectedBody, $body)
        );
    }

    private function compareSameBodyResponse(Pact $p, StreamInterface $expected, StreamInterface $body)
    {
        $expected->rewind();
        $body->rewind();

        $expectedBody = json_decode($expected->getContents(), true);
        $body = json_decode($body->getContents(), true);

        if($expectedBody) {
            $this->assertKeyRecursively($p, $expectedBody, $body);
        } else {
            $this->assertEquals($expectedBody, $body);
        }
    }

    private function assertKeyRecursively(Pact $p, $expectedBody, $body)
    {
        foreach($expectedBody as $key => $value) {

            $this->assertArrayHasKey(
                $key,
                $body,
                sprintf("Test: %s \n , Body key mismatch Expected \"%s\", Got Keys: \"%s\"", $p->getDescription(), $key, json_encode(array_keys($body)))); //skip value compare for now

            if(is_array($value)) {
                $this->assertKeyRecursively($p, $value, $body[$key]);
            }
        }
    }

}