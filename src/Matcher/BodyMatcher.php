<?php

namespace Erlangb\Phpacto\Matcher;

use Erlangb\Phpacto\Diff\Diff;
use Erlangb\Phpacto\Diff\Mismatch;
use Erlangb\Phpacto\Diff\MismatchType;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class BodyMatcher
{
    const LOCATION = 'Body';

    public function match(MessageInterface $expected, MessageInterface $actual)
    {
        return $this->compareBodyResponse($expected->getBody(), $actual->getBody());
    }

    private function compareBodyResponse(StreamInterface $expected, StreamInterface $actualBody)
    {
        $diff = new Diff();

        $expected->rewind();
        $actualBody->rewind();

        $expectedBody = json_decode($expected->getContents(), true);
        $body = json_decode($actualBody->getContents(), true);

        if ($expectedBody) {
            $this->getDiffRecursively($expectedBody, $body, $diff);
        } elseif ($body !== $expectedBody) {
            $diff->add(
                new Mismatch(
                    self::LOCATION,
                    MismatchType::NIL_VS_NOT_NULL
                )
            );
        }

        return $diff;
    }

    private function getDiffRecursively($expectedBody, $actualBody, Diff $diff)
    {
        foreach ($expectedBody as $key => $value) {
            if (!key_exists($key, $actualBody)) {
                $diff->add(
                    new Mismatch(
                        self::LOCATION,
                        MismatchType::KEY_NOT_FOUND,
                        [$key]
                    )
                );
            }

            if (is_array($value)) {
                $this->getDiffRecursively($value, $actualBody[$key], $diff);
            }
        }

        return $diff;
    }
}
