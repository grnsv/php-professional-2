<?php

namespace App\Container;

use ReflectionClass;
use App\Traits\Singleton;
use App\Exceptions\NotFoundException;
use Psr\Container\ContainerInterface;

class DIContainer implements ContainerInterface
{
    use Singleton;

    private function __construct()
    {
    }

    private array $resolvers = [];

    public function bind(string $type, string|object $resolver): self
    {
        $this->resolvers[$type] = $resolver;
        return $this;
    }

    public function get(string $type): object
    {
        if (array_key_exists($type, $this->resolvers)) {
            $typeToCreate = $this->resolvers[$type];

            if (is_object($typeToCreate)) {
                return $typeToCreate;
            }

            return $this->get($typeToCreate);
        }

        if (!class_exists($type)) {
            throw new NotFoundException("Cannot resolve type: $type");
        }

        $reflectionClass = new ReflectionClass($type);
        $constructor = $reflectionClass->getConstructor();

        if (!$constructor) {
            return new $type();
        }

        $parameters = [];

        foreach ($constructor->getParameters() as $parameter) {

            $parameterType = $parameter->getType()->getName();
            $parameters[] = $this->get($parameterType);
        }

        return new $type(...$parameters);
    }

    public function has(string $type): bool
    {
        try {
            $this->get($type);
        } catch (NotFoundException) {
            return false;
        }
        return true;
    }
}
