<?php

namespace Tests\Container;

use App\Drivers\Connection;
use App\config\SqliteConfig;
use App\Container\DIContainer;
use PHPUnit\Framework\TestCase;
use App\Drivers\PdoConnectionDriver;
use App\Repositories\UserRepository;
use App\Exceptions\NotFoundException;
use App\Repositories\UserRepositoryInterface;

class DIContainerTest extends TestCase
{
    public function testItThrowsAnExceptionIfCannotResolveType(): void
    {
        $container = $this->getDIContainer();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'Cannot resolve type: Tests\Container\SomeClass'
        );

        $container->get(SomeClass::class);
    }

    public function testItResolvesClassWithoutDependencies(): void
    {
        $container = $this->getDIContainer();

        $object = $container->get(SomeClassWithoutDependencies::class);

        $this->assertInstanceOf(
            SomeClassWithoutDependencies::class,
            $object
        );
    }

    public function testItResolvesClassByContract(): void
    {
        $container = $this->getDIContainer();

        $container->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $container->bind(
            Connection::class,
            PdoConnectionDriver::getInstance(SqliteConfig::DSN)
        );

        $object = $container->get(UserRepositoryInterface::class);

        $this->assertInstanceOf(
            UserRepository::class,
            $object
        );
    }

    public function testItReturnsPredefinedObject(): void
    {
        $container = $this->getDIContainer();

        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );

        $object = $container->get(SomeClassWithParameter::class);

        $this->assertInstanceOf(
            SomeClassWithParameter::class,
            $object
        );

        $this->assertSame(42, $object->value());
    }

    public function testItResolvesClassWithDependencies(): void
    {
        $container = $this->getDIContainer();

        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(10000)
        );

        $object = $container->get(ClassDependingOnAnother::class);

        $this->assertInstanceOf(
            ClassDependingOnAnother::class,
            $object
        );
    }

    private function getDIContainer(): DIContainer
    {
        return DIContainer::getInstance();
    }
}
