<?php

namespace Erlangb\Phpacto\Test;

use Erlangb\Phpacto\Consumer\Pact;
use Erlangb\Phpacto\Consumer\PactList;
use Erlangb\Phpacto\Factory\Pacto\PactListFactory;
use Symfony\Component\Finder\Finder;

class PactoIntegrationTest
{
    /** @var  PactList[] */
    private $contracts;
    private $providerName;
    private $strict;

    public function __construct($providerName, $strict = false)
    {
        $this->providerName = $providerName;
        $this->strict = $strict;
    }

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
     * @param \Closure $makeRequest How make a ps7Request
     * @param \Closure $loadState Setup the test state
     * @param \Closure $down Setup up back the state
     */
    public function honorContracts(\Closure $makeRequest, \Closure $loadState, \Closure $down)
    {
       $contracts = $this->getContractsFor($this->providerName);

        if(count($contracts) === 0) {
            throw new \Exception('No contracts found');
        }

        $runner = new \PHPUnit_TextUI_TestRunner();
        $suite = new \PHPUnit_Framework_TestSuite();

       foreach($this->getAllPactsInContracts($contracts) as $pact) {
           $t = new PactoInstanceTest(
               "testItHonorContract",
               $down,
               $loadState,
               $makeRequest,
               $pact,
               $this->strict
           );

           $suite->addTest($t);
       }
        $runner->run($suite, ['colors' =>  \PHPUnit_TextUI_ResultPrinter::COLOR_ALWAYS]);
    }

    /**
     * @param $contracts
     * @return Pact[]
     */
    private function getAllPactsInContracts($contracts)
    {
        $interactions = [];
        foreach($contracts as $contract) {
            $interactions = array_merge($contract->getInteractions(), $interactions);
        }

        return $interactions;
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

    private function checkIfFolderContainsFiles($folder)
    {
        $finder = new Finder();
        if($finder->files()->in($folder)->count() === 0) {
            throw new \RuntimeException(sprintf('There is any files in %s', $folder));
        }

    }
}