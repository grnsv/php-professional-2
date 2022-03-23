<?php

namespace App\Factories;

use App\Commands\GetCommand;
use JetBrains\PhpStorm\Pure;
use App\Entities\Comment\Comment;
use App\Decorator\CommentDecorator;


final class CommentFactory implements CommentFactoryInterface
{
    #[Pure] public function create(CommentDecorator $commentDecorator): Comment
    {
        /**
         * @var EntityManagerFactoryInterface $entityManager
         */
        $entityManager = EntityManagerFactory::getInstance();
        $command = new GetCommand($entityManager->getRepository('user'));
        $author = $command->handle($commentDecorator->authorId);
        $command = new GetCommand($entityManager->getRepository('article'));
        $article = $command->handle($commentDecorator->articleId);
        return new Comment(
            $author,
            $article,
            $commentDecorator->text,
        );
    }
}
