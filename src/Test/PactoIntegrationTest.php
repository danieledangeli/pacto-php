<?php

namespace Erlangb\Phpacto\Test;

use Erlangb\Phpacto\Consumer\Pact;
use Erlangb\Phpacto\Consumer\PactList;
use Erlangb\Phpacto\Factory\Pacto\PactListFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\Finder\Finder;

class PactoIntegrationTest extends \PHPUnit_Framework_TestCase
{
    /** @var  PactList[] */
    protected $contracts;

    public function loadContracts($folder)
    {
        $this->checkIfFolderContainsFiles($folder);

        $finder = new Finder();
        $finder->files()->in($folder);

        $contractFactory = PactListFactory::getPactoListFactory();

        foreach ($finder as $file) {
            $this->contracts[] = $contractFactory->from(file_get_contents($file->getRealpath()));
        }
    }

    /**
     * @param $providerName
     * @return PactList[]
     */
    public function getContractsFor($providerName)
    {
        $contracts = [];

        foreach($this->contracts as $pactList) {
            if($pactList->matchProvider($providerName)) {
                $contracts[] = $pactList;
            }
        }

        return $contracts;
    }

    /**
     * @return Pact[]
     */
    public function getPactsByDescription($providerName, $description)
    {
        $contracts = $this->getContractsFor($providerName);
        $pacts = [];

        foreach($contracts as $contract) {
            $pacts = array_merge($pacts, $contract->filterPactsByDescription($description));
        }

        return $pacts;
    }

    /**
     * @return Pact[]
     */
    public function getPactsByProviderState($providerName, $providerState)
    {
        $contracts = $this->getContractsFor($providerName);
        $pacts = [];

        foreach($contracts as $contract) {
            $pacts = array_merge($pacts, $contract->filterPactsByProviderState($providerState));
        }

        return $pacts;
    }

    /**
     * @return Pact[]
     */
    public function getPactsByDescriptionAndState($providerName, $description, $providerState)
    {
        $contracts = $this->getContractsFor($providerName);
        $pacts = [];

        foreach($contracts as $contract) {
            $pacts = array_merge($pacts, $contract->filterPactsByDescrioptionAndProviderState($description, $providerState));
        }

        return $pacts;
    }

    public function assertResponse(Pact $p, ResponseInterface $r, $strict = true)
    {
        if($strict) {
            $this->assertStrictResponse($p->getResponse(), $r);
        } else {
            $this->assertSameResponse($p->getResponse(), $r);
        }
    }

    private function checkIfFolderContainsFiles($folder)
    {
        $finder = new Finder();
        if($finder->files()->in($folder)->count() === 0) {
            throw new \RuntimeException(sprintf('There is any files in %s', $folder));
        }

    }

    private function assertStrictResponse(ResponseInterface $expectedResponse, ResponseInterface $response)
    {
        $this->assertSameResponse($expectedResponse, $response);
        $this->compareBodyResponse($expectedResponse->getBody(), $response->getBody());
    }

    private function assertSameResponse(ResponseInterface $expectedResponse, ResponseInterface $response)
    {
        $this->compareStatusCodes($expectedResponse->getStatusCode(), $response->getStatusCode());
        $this->compareHeaders($expectedResponse->getHeaders(), $response->getHeaders());
        $this->compareSameBodyResponse($expectedResponse->getBody(), $response->getBody());
    }

    private function compareStatusCodes($expected, $statusCode)
    {
        $this->assertEquals($expected, $statusCode, "Status Code Expectation Failed");
    }

    private function compareHeaders($expectedHeaders, $headers)
    {
        foreach($expectedHeaders as $expectedKey => $expectedValue) {
            $this->assertArrayHasKey(
                $expectedKey,
                $headers,
                sprintf('Missed key in response headers', $expectedKey, json_encode($headers))
            );

            $this->assertEquals(
                $expectedValue[0],
                $headers[$expectedKey][0],
                sprintf('Headers content mismatch: Expected %s Got: %s', $expectedValue[0], $headers[$expectedKey][0])
            );
        }
    }

    private function compareBodyResponse(StreamInterface $expected, StreamInterface $body)
    {
        $expected->rewind();
        $body->rewind();

        $expectedBody = $expected->getContents();
        $body = $body->getContents();

        $this->assertEquals(
            $expectedBody,
            $body,
            sprintf('Body mismatch Expected: %s, Got: %s', $expectedBody, $body)
        );
    }

    private function compareSameBodyResponse(StreamInterface $expected, StreamInterface $body)
    {
        $expected->rewind();
        $body->rewind();

        $expectedBody = json_decode($expected->getContents(), true);
        $body = json_decode($body->getContents(), true);

        foreach($expectedBody as $key => $value) {
            $this->assertArrayHasKey(
                $key,
                $body,
                sprintf('Body key mismatch Expected %s, Got Keys: %s', $key, json_encode(array_keys($body)))); //skip value compare for now
        }
    }
}