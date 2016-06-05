<?php

namespace Erlangb\Phpacto\Diff;

class Diff
{
    /**
     * @var Mismatch[]
     */
    private $mismatches;

    public function __construct($mismatches = [])
    {
        $this->mismatches = $mismatches;
    }

    public function add(Mismatch $mismatch)
    {
        $this->mismatches[] = $mismatch;
    }

    public function hasMismatches()
    {
        return count($this->mismatches) > 0;
    }

    /**
     * @return Mismatch[]
     */
    public function getMismatches()
    {
        return $this->mismatches;
    }

    /**
     * @return Diff
     */
    public function merge()
    {
        $mismatches = [];

        foreach (func_get_args() as $diff) {
            $mismatches = array_merge($mismatches, $diff->getMismatches());
        }

        return new Diff(array_merge($this->mismatches, $mismatches));
    }
}
