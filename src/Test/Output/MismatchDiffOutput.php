<?php

namespace Erlangb\Phpacto\Test\Output;

use Erlangb\Phpacto\Diff\Diff;
use Erlangb\Phpacto\Pact\Pact;
use SebastianBergmann\Exporter\Exporter;

class MismatchDiffOutput
{
    private $verbose;

    public function __construct($verbose = false)
    {
        $this->verbose = $verbose;
    }

    public function getOutputFor(Diff $diff, Pact $pact)
    {
        $mismatches = $diff->getMismatches();
        $output = $pact->getDescription() . "\n" . $pact->getProviderState();
        $errors = 0;

        foreach ($mismatches as $mismatch) {
            $errors++;

            $output .= sprintf(
                "\n Error# %s: \n Location: %s \n Error: %s \n",
                $errors, $mismatch->getLocation(), $mismatch
            );
        }

        return $output;
    }
}
