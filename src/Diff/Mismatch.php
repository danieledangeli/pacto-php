<?php

namespace Erlangb\Phpacto\Diff;

class Mismatch
{
    private $mismatchType;
    private $mismatchAt;
    private $mismatchMessage;

    private $expected;
    private $received;

    public function __construct($mismatchAt, $mismatchType, array $mismatchArgs = [], $expected, $received)
    {
        $this->mismatchType = $mismatchType;
        $this->mismatchAt = $mismatchAt;
        $this->mismatchMessage = vsprintf($mismatchType, $mismatchArgs);
        $this->expected = $expected;
        $this->received = $received;
    }

    public function getMismatchType()
    {
        return $this->mismatchType;
    }

    public function getLocation()
    {
        return $this->mismatchAt;
    }

    /**
     * @return mixed
     */
    public function getExpected()
    {
        return $this->expected;
    }

    /**
     * @return mixed
     */
    public function getReceived()
    {
        return $this->received;
    }

    public function __toString()
    {
        return sprintf(
            "mismatch at %s: %s", $this->mismatchAt, $this->mismatchMessage
        );
    }
}
