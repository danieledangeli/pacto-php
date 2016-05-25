<?php

namespace Erlangb\Phpacto\Test;

use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class PactoIntegrationTestTest extends PactoIntegrationTest
{
    public function setUp()
    {
        parent::setUp();
        $this->loadContracts(__DIR__.'/../fixtures');
    }

    public function testItShouldLoadContracts()
    {
        $this->assertCount(2, $this->contracts);
    }

    public function testItShouldGetsContractsByProviderName()
    {
        $contracts = $this->getContractsFor('Animal Service');
        $contractsOne = $this->getContractsFor('Animal Service 2');
        $contractsZero = $this->getContractsFor('Animal Service 444444');

        $this->assertCount(1, $contracts);
        $this->assertCount(1, $contractsOne);
        $this->assertCount(0, $contractsZero);
    }

    public function testItShouldGetPactsByDescription()
    {
        //there are actually 2 pacts named a request for an alligator in provider Animal Service 2
        $pacts = $this->getPactsByDescription("Animal Service 2", "a request for an alligator");
        $this->assertCount(2, $pacts);
    }

    public function testItShouldGetZerpPactsWhenProviderDoesNotExists()
    {
        //there are actually 2 pacts named a request for an alligator in provider Animal Service 2
        $pacts =$this->getPactsByDescription("No provider", "a request for an alligator");
        $this->assertCount(0, $pacts);
    }

    public function testItShouldGetZeroPactsWhenDescriptionDoesNotExists()
    {
        //there are actually 2 pacts named a request for an alligator in provider Animal Service 2
        $pacts =$this->getPactsByDescription("Animal Service 2", "a fake request for an alligator");
        $this->assertCount(0, $pacts);
    }

    public function testItShouldGetPactsByProviderState()
    {
        $pacts = $this->getPactsByProviderState("Animal Service 2", "there is not an alligator named Mary");
        $this->assertCount(1, $pacts);
    }

    public function testItShouldGetPactsByDescriptionProviderState()
    {
        $pacts =$this->getPactsByDescriptionAndState(
            "Animal Service 2",
            "a 404 request for an alligator",
            "there is not an alligator named Mary"
        );

        $this->assertCount(1, $pacts);
    }

    public function testItCompareStrictSameResponses()
    {
        $pact = $this->getPactsByDescriptionAndState(
            "Animal Service",
            "a request for an alligator",
            "there is an alligator named Mary"
        );

        $response = $this->loadResponseForPactoRequestForAnAlligator();
        $this->assertResponse($pact[0], $response, true);
    }

    public function testItCompareSameResponses()
    {
        $pact = $this->getPactsByDescriptionAndState(
            "Animal Service",
            "a request for an alligator",
            "there is an alligator named Mary"
        );

        $response = $this->loadResponseForPactoRequestForAnAlligator(
            ['name' => 'skip the content', 'extra field' => 'dont-give-a-fuck']
        );

        $this->assertResponse($pact[0], $response, false);
    }

    public function testItShouldFailWhenStatusCodeIsNotHonored()
    {
        $pact = $this->getPactsByDescriptionAndState(
            "Animal Service",
            "a request for an alligator",
            "there is an alligator named Mary"
        );

        $response = $this->loadResponseForPactoRequestForAnAlligator(
            ['name' => 'Mary'],
            201
        );

        try {
            $this->assertResponse($pact[0], $response);
            $this->fail("Failing asserting that response are not equals");
        } catch(\PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertContains("Status Code Expectation Failed", $e->getMessage());
        }
    }

    public function testItShouldFailWhenHeadersAreNotHonored()
    {
        $pact = $this->getPactsByDescriptionAndState(
            "Animal Service",
            "a request for an alligator",
            "there is an alligator named Mary"
        );

        $response = $this->loadResponseForPactoRequestForAnAlligator(
            ['name' => 'Mary'],
            200,
            ['Content-Type' => 'application/json;charset=utf']
        );

        try {
            $this->assertResponse($pact[0], $response);
            $this->fail("Failing asserting that response are not equals");
        } catch(\PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertContains("Headers content mismatch", $e->getMessage());
        }
    }

    public function testItShouldFailWhenHeadersKeysAreNotHonored()
    {
        $pact = $this->getPactsByDescriptionAndState(
            "Animal Service",
            "a request for an alligator",
            "there is an alligator named Mary"
        );

        $response = $this->loadResponseForPactoRequestForAnAlligator(
            ['name' => 'Mary'],
            200
        );

        $response = $response->withoutHeader('Content-Type');

        try {
            $this->assertResponse($pact[0], $response);
            $this->fail("Failing asserting that response are not equals");
        } catch(\PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertContains("Missed key in response headers", $e->getMessage());
        }
    }

    public function testItShouldFailWithContentMismatch()
    {
        $pact = $this->getPactsByDescriptionAndState(
            "Animal Service",
            "a request for an alligator",
            "there is an alligator named Mary"
        );

        $response = $this->loadResponseForPactoRequestForAnAlligator(['name' => 'MaryJO']);

        try {
            $this->assertResponse($pact[0], $response, true);
            $this->fail("Failing asserting that response are not equals");
        } catch(\PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertContains("Body mismatch", $e->getMessage());
        }
    }

    public function testItShouldFailWithContentMismatchDuringNoStrictBodyComparision()
    {
        $pact = $this->getPactsByDescriptionAndState(
            "Animal Service",
            "a request for an alligator",
            "there is an alligator named Mary"
        );

        $response = $this->loadResponseForPactoRequestForAnAlligator();
        $stream = new Stream('php://memory', 'w');
        $stream->write(json_encode(['noname' => 'Mary']));
        $response = $response->withBody($stream);

        try {
            $this->assertResponse($pact[0], $response, false);
            $this->fail("Failing asserting that response are not equals");
        } catch(\PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertContains("Body key mismatch", $e->getMessage());
        }
    }
    
    private function loadResponseForPactoRequestForAnAlligator($extraFields = [], $statusCode = 200, $extraHeaders = [])
    {
        $stream = new Stream('php://memory', 'w');
        $stream->write(json_encode(array_merge(['name' => 'Mary'], $extraFields)));

        $response = new Response(
            $stream,
            $statusCode
        );

        $headers = [];
        $headers['Content-Type'] = 'application/json;charset=utf-8';
        $headers['X-Custom-Framework-Header'] = 'any extra header value';

        $headers = array_merge($headers, $extraHeaders);

        foreach($headers as $key => $header) {
            $response = $response->withAddedHeader($key, $header);
        }

        return $response;
    }
}