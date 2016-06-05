<?php

namespace Erlangb\Phpacto\Matcher;

use Erlangb\Phpacto\Diff\Diff;
use Erlangb\Phpacto\Diff\Mismatch;
use Erlangb\Phpacto\Diff\MismatchType;
use Psr\Http\Message\MessageInterface;

class HeadersMatcher
{
    const LOCATION = 'Header';

    public function match(MessageInterface $expected, MessageInterface $actual)
    {
        $expectedHeaders = $this->normalizeKeys($expected->getHeaders());
        $actualHeaders = $this->normalizeKeys($actual->getHeaders());

        return $this->compareHeaders($expectedHeaders, $actualHeaders);
    }

    private function normalizeKeys($headers)
    {
        return array_change_key_case($headers, CASE_LOWER);
    }

    private function compareHeaders($expectedHeaders, $actualHeaders)
    {
        $diff = $this->getDiffInHeadersKeys($expectedHeaders, $actualHeaders);
        $diff = $diff->merge($this->getDiffInHeadersValues($expectedHeaders, $actualHeaders));

        return $diff;
    }

    private function getDiffInHeadersKeys($expectedHeaders, $actualHeaders)
    {
        $diff = new Diff();

        $keys = array_diff(array_keys($expectedHeaders), array_keys($actualHeaders));

        foreach ($keys as $key) {
            $diff->add(
                new Mismatch(
                    self::LOCATION,
                    MismatchType::KEY_NOT_FOUND,
                    [$key],
                    $expectedHeaders,
                    $actualHeaders
                )
            );
        }

        return $diff;
    }

    private function getDiffInHeadersValues($expectedHeaders, $actualHeaders)
    {
        $diff = new Diff();

        foreach ($expectedHeaders as $key => $expectedValues) {
            if (key_exists($key, $actualHeaders)) {
                $actualValues = $actualHeaders[$key];
                $values = array_diff(array_values($expectedValues), array_values($actualValues));
                $reverseValues = array_diff(array_values($actualValues), array_values($expectedValues));

                if (count($values) > 0 || count($reverseValues) > 0) {
                    $diff->add(
                        new Mismatch(
                            self::LOCATION,
                            MismatchType::UNEQUAL,
                            [implode(',', $expectedValues), implode(',', $actualValues)],
                            $expectedHeaders,
                            $actualHeaders
                        )
                    );
                }
            }
        }

        return $diff;
    }
}
