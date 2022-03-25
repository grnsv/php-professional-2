<?php

namespace App\Factories;

use App\Entities\User\User;
use App\Commands\GetCommand;
use JetBrains\PhpStorm\Pure;
use App\Entities\Article\Article;
use App\Entities\Comment\Comment;
use App\Decorator\CommentDecorator;

final class CommentFactory implements CommentFactoryInterface
{
    #[Pure] public function create(CommentDecorator $commentDecorator): Comment
    {
        /**
         * @var EntityManagerFactoryInterface $entityMangerFactory
         */
        $entityMangerFactory = EntityManagerFactory::getInstance();
        $command = new GetCommand($entityMangerFactory->getRepository(User::class));
        $author = $command->handle($commentDecorator->authorId);
        $command = new GetCommand($entityMangerFactory->getRepository(Article::class));
        $article = $command->handle($commentDecorator->articleId);
        return new Comment(
            $author,
            $article,
            $commentDecorator->text,
        );
    }
}
