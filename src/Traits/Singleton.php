<?php

namespace App\Traits;

trait Singleton
{
    protected static array $instances = [];
    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        $class = static::class;
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static();
        }

        return self::$instances[$class];
    }
}
