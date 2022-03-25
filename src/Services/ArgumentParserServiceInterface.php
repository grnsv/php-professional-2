<?php

namespace App\Services;

use App\Classes\ArgumentInterface;

interface ArgumentParserServiceInterface
{
    public function parseRawInput(iterable $rawInput, array $scheme): ArgumentInterface;
}
