<?php

namespace Erlangb\Phpacto\Factory\Pacto;

use Erlangb\Phpacto\Factory\ContractFactoryInterface;
use Erlangb\Phpacto\Factory\PactoRequestFactoryInterface;
use Erlangb\Phpacto\Factory\PactoResponseFactoryInterface;
use Erlangb\Phpacto\Pact\Pact;
use Erlangb\Phpacto\Pact\PactList;

class PactListFactory implements ContractFactoryInterface
{
    private $pactoRequestFactory;
    private $pactoResponseFactory;

    public function __construct(
        PactoRequestFactoryInterface $pactoRequestFactory,
        PactoResponseFactoryInterface $pactoResponseFactory)
    {
        $this->pactoRequestFactory = $pactoRequestFactory;
        $this->pactoResponseFactory = $pactoResponseFactory;
    }

    public function from($jsonDescription)
    {
        $jsonDescription = json_decode($jsonDescription, true);
        $provider = $jsonDescription['provider']['name'];
        $consumer = $jsonDescription['consumer']['name'];

        $pactList = new PactList($provider, $consumer);

        foreach ($jsonDescription['interactions'] as $interaction) {
            $pact = new Pact(
                $this->pactoRequestFactory->from($interaction['request']),
                $this->pactoResponseFactory->from($interaction['response']),
                $interaction['description'],
                $interaction['provider_state']
            );

            $pactList->add($pact);
        }

        return $pactList;
    }

    public static function getPactoListFactory()
    {
        return new self(
            new PactoRequestFactory(),
            new PactoResponseFactory()
        );
    }
}
