<?php

namespace Erlangb\Phpacto\Matcher;

use Erlangb\Phpacto\Diff\Diff;
use Erlangb\Phpacto\Diff\Mismatch;
use Erlangb\Phpacto\Diff\MismatchType;
use Psr\Http\Message\ResponseInterface;

class StatusCodeMatcher
{
    const LOCATION = 'Status Code';

    /**
     * @param ResponseInterface $expected
     * @param ResponseInterface $actual
     *
     * @return Diff
     */
    public function match(ResponseInterface $expected, ResponseInterface $actual)
    {
        $diff = new Diff();

        if ($expected->getStatusCode() !== $actual->getStatusCode()) {
            $diff->add(
                new Mismatch(
                    self::LOCATION,
                    MismatchType::UNEQUAL,
                    [$expected->getStatusCode(), $actual->getStatusCode()]
                )
            );
        }

        return $diff;
    }
}
