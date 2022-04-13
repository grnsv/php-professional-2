<?php

use Dotenv\Dotenv;
use Monolog\Logger;
use Faker\Provider\Lorem;
use App\Drivers\Connection;
use Psr\Log\LoggerInterface;
use App\Container\DIContainer;
use Faker\Provider\ru_RU\Text;
use Faker\Provider\ru_RU\Person;
use App\Queries\TokenQueryHandler;
use Faker\Provider\ru_RU\Internet;
use Monolog\Handler\StreamHandler;
use App\Drivers\PdoConnectionDriver;
use App\Repositories\LikeRepository;
use App\Repositories\UserRepository;
use App\Commands\TokenCommandHandler;
use App\Repositories\ArticleRepository;
use App\Repositories\CommentRepository;
use App\Http\Auth\PasswordAuthentication;
use App\Http\Auth\IdentificationInterface;
use App\Repositories\AuthTokensRepository;
use App\Queries\TokenQueryHandlerInterface;
use App\Http\Auth\BearerTokenAuthentication;
use App\Repositories\LikeRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\Commands\TokenCommandHandlerInterface;
use App\Http\Auth\TokenAuthenticationInterface;
use App\Repositories\ArticleRepositoryInterface;
use App\Repositories\CommentRepositoryInterface;
use App\Http\Auth\JsonBodyUserEmailIdentification;
use App\Http\Auth\PasswordAuthenticationInterface;
use App\Repositories\AuthTokensRepositoryInterface;

require_once __DIR__ . '/vendor/autoload.php';

Dotenv::createImmutable(__DIR__)->safeLoad();

$container = DIContainer::getInstance();

$faker = new \Faker\Generator();

$faker->addProvider(new Person($faker));
$faker->addProvider(new Text($faker));
$faker->addProvider(new Internet($faker));
$faker->addProvider(new Lorem($faker));

$container->bind(
    \Faker\Generator::class,
    $faker
);

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
    AuthTokensRepositoryInterface::class,
    AuthTokensRepository::class
);

$container->bind(
    Connection::class,
    PdoConnectionDriver::getInstance($_SERVER['DSN_DATABASE'])
);

$container->bind(
    IdentificationInterface::class,
    JsonBodyUserEmailIdentification::class
);

$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);

$container->bind(
    TokenAuthenticationInterface::class,
    BearerTokenAuthentication::class
);

$container->bind(
    TokenQueryHandlerInterface::class,
    TokenQueryHandler::class
);

$container->bind(
    TokenCommandHandlerInterface::class,
    TokenCommandHandler::class
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
