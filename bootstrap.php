<?php

use Dotenv\Dotenv;
use Monolog\Logger;
use App\Drivers\Connection;
use Psr\Log\LoggerInterface;
use App\Container\DIContainer;
use Monolog\Handler\StreamHandler;
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

Dotenv::createImmutable(__DIR__)->safeLoad();

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
    PdoConnectionDriver::getInstance($_SERVER['DSN_DATABASE'])
);


$logger = new Logger('geekbrains');

$isNeedLogToFile = (bool)$_SERVER['LOG_TO_FILES'];
$isNeedLogToConsole = (bool)$_SERVER['LOG_TO_CONSOLE'];

if ($isNeedLogToFile) {
    $logger
        ->pushHandler(
            new StreamHandler(
                __DIR__ . '/.logs/geekbrains.log'
            )
        )
        ->pushHandler(
            new StreamHandler(
                __DIR__ . '/.logs/geekbrains.error.log',
                level: Logger::ERROR,
                bubble: false,
            )
        );
}

if ($isNeedLogToConsole) {
    $logger
        ->pushHandler(
            new StreamHandler(
                "php://stdout"
            )
        );
}

$container->bind(
    LoggerInterface::class,
    $logger
);

return $container;
