<?php

use Symfony\Component\Console\Application;
use App\Commands\SymfonyCommands\CreateUser;
use App\Commands\SymfonyCommands\PopulateDB;
use App\Commands\SymfonyCommands\UpdateUser;
use App\Commands\SymfonyCommands\CreateComment;
use App\Commands\SymfonyCommands\DeleteArticle;

$container = require __DIR__ . '/bootstrap.php';
$application = new Application();

$commandsClasses =
    [
        CreateUser::class,
        DeleteArticle::class,
        PopulateDB::class,
        UpdateUser::class,
        CreateComment::class,
    ];

foreach ($commandsClasses as $commandClass) {
    $command = $container->get($commandClass);
    $application->add($command);
}

$application->run();
