<?php

namespace App\Decorator;

use App\Classes\ArgumentInterface;

interface DecoratorInterface
{
    public function getFieldData(): ArgumentInterface;
    public function getRequiredFields(): array;
}
