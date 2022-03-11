<?php

namespace App\Services;

use App\Classes\ArgumentInterface;

interface ArgumentParserServiceInterface
{
    /**
     * @return Argument
     */
    public function parseRawInput(iterable $rawInput, array $scheme): ArgumentInterface;
}
