<?php

namespace Erlangb\Phpacto\Consumer;

class PactList
{
    private $provider;
    private $consumer;

    private $interactions;

    public function __construct($provider, $consumer, $interactions = [])
    {
        $this->provider = $provider;
        $this->consumer = $consumer;
        $this->interactions = $interactions;
    }

    public function add(Pact $pact)
    {
        $this->interactions[] = $pact;
    }

    public function all()
    {
        return  $this->interactions;
    }

    public function getConsumer()
    {
        return $this->consumer;
    }

    public function getProvider()
    {
        return $this->provider;
    }

    public function matchProvider($provider)
    {
        return strtolower($provider) == strtolower($this->provider);
    }

    /**
     * @param $description
     * @return Pact[]
     */
    public function filterPactsByDescription($description)
    {
         return array_filter($this->all(), function(Pact $p) use($description) {
            return strtolower($p->getDescription()) == strtolower($description);
        });
    }

    /**
     * @param $providerState
     * @return Pact[]
     */
    public function filterPactsByProviderState($providerState)
    {
        return array_filter($this->all(), function(Pact $p) use($providerState) {
            return strtolower($p->getProviderState()) == strtolower($providerState);
        });
    }

    /**
     * @param $providerState
     * @return Pact[]
     */
    public function filterPactsByDescrioptionAndProviderState($description, $providerState)
    {
        return array_filter($this->all(), function(Pact $p) use($description, $providerState) {
            return strtolower($p->getProviderState()) == strtolower($providerState)
            && strtolower($p->getDescription()) == strtolower($description);
        });
    }

    /**
     * @return Pact[]
     */
    public function getInteractions()
    {
        return $this->interactions;
    }
}