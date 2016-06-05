<?php

namespace Erlangb\Phpacto\Diff;

final class MismatchType
{
    const TYPE_MISMATCH = "type mismatch expected %s received %s";
    const LENGTH_MISMATCH = "length mismatch, expected %d received %d";
    const UNEQUAL = "unequal expected %s received %s";
    const VALIDITY = "validity mismatch";
    const FIELD_UNEXPECTED = "unexpected field %s";
    const FIELD_NOT_FOUND = "field %s not found";
    const KEY_NOT_FOUND = "key %s not found";
    const NIL_VS_NOT_NULL = "nil vs non-nil mismatch";
    const NON_NIL_FUNCTIONS = "non-nil functions";
}
