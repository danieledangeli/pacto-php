<?php

namespace Erlangb\Phpacto\Diff;

class Mismatch
{
    private $mismatchType;
    private $mismatchAt;
    private $mismatchMessage;

    private $expected;
    private $received;

    public function __construct($mismatchAt, $mismatchType, array $mismatchArgs = [])
    {
        $this->mismatchType = $mismatchType;
        $this->mismatchAt = $mismatchAt;
        $this->mismatchMessage = vsprintf($mismatchType, $mismatchArgs);
    }

    public function getMismatchType()
    {
        return $this->mismatchType;
    }

    public function getLocation()
    {
        return $this->mismatchAt;
    }

    public function __toString()
    {
        return sprintf(
            "mismatch at %s: %s", $this->mismatchAt, $this->mismatchMessage
        );
    }
}
