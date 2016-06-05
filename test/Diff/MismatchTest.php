<?php

namespace Erlangb\Phpacto\Diff;

class MismatchTest extends \PHPUnit_Framework_TestCase
{
    public function testItPrintsMismatch()
    {
        $mismatch = new Mismatch("location", MismatchType::UNEQUAL, ['a', 'b']);
        $this->assertEquals('mismatch at location: unequal expected a received b', $mismatch);
    }
}