<?php

namespace Erlangb\Phpacto;

class Fixture
{
    public static function load($fixureName)
    {
        $content = file_get_contents(sprintf(__DIR__.'/fixtures/%s', $fixureName));
        return $content;
    }
}