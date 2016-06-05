<?php

namespace Erlangb\Phpacto\Factory;

use Erlangb\Phpacto\Pact\Pact;

interface ContractFactoryInterface
{
    /**
     * @param $jsonDescription
     * @return Pact
     */
    public function from($jsonDescription);
}
