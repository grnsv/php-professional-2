<?php

require_once 'vendor/autoload.php';

use Faker\Factory;
use GeekBrains\Blog\Post;
use GeekBrains\User\User;
use GeekBrains\Blog\Comment;

$faker = Factory::create();

switch ($argv[1]) {

    case 'user':
        $user = new User(
            rand(),
            $faker->firstName(),
            $faker->lastName(),
            new DateTimeImmutable(),
        );
        echo $user . PHP_EOL;
        break;

    case 'post':
        $post = new Post(
            rand(),
            new User(
                rand(),
                $faker->firstName(),
                $faker->lastName(),
                new DateTimeImmutable(),
            ),
            $faker->text(50),
            $faker->text(100)
        );
        echo $post . PHP_EOL;
        break;

    case 'comment':
        $comment = new Comment(
            rand(),
            new User(
                rand(),
                $faker->firstName(),
                $faker->lastName(),
                new DateTimeImmutable(),
            ),
            new Post(
                rand(),
                new User(
                    rand(),
                    $faker->firstName(),
                    $faker->lastName(),
                    new DateTimeImmutable(),
                ),
                $faker->text(50),
                $faker->text(100)
            ),
            $faker->text(50)
        );
        echo $comment . PHP_EOL;
        break;
}
