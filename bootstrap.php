<?php

use App\Drivers\Connection;
use App\config\SqliteConfig;
use App\Container\DIContainer;
use App\Drivers\PdoConnectionDriver;
use App\Repositories\LikeRepository;
use App\Repositories\UserRepository;
use App\Repositories\ArticleRepository;
use App\Repositories\CommentRepository;
use App\Repositories\LikeRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\ArticleRepositoryInterface;
use App\Repositories\CommentRepositoryInterface;

require_once __DIR__ . '/vendor/autoload.php';

$container = DIContainer::getInstance();

$container->bind(
    UserRepositoryInterface::class,
    UserRepository::class
);

$container->bind(
    ArticleRepositoryInterface::class,
    ArticleRepository::class
);

$container->bind(
    CommentRepositoryInterface::class,
    CommentRepository::class
);

$container->bind(
    LikeRepositoryInterface::class,
    LikeRepository::class
);

$container->bind(
    Connection::class,
    PdoConnectionDriver::getInstance(SqliteConfig::DSN)
);

return $container;
