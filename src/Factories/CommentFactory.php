<?php

namespace App\Factories;

use JetBrains\PhpStorm\Pure;
use App\Container\DIContainer;
use App\Entities\Comment\Comment;
use App\Decorator\CommentDecorator;
use App\Repositories\UserRepository;
use App\Repositories\ArticleRepository;

final class CommentFactory implements CommentFactoryInterface
{
    #[Pure] public function create(CommentDecorator $commentDecorator): Comment
    {
        /**
         * @var DIContainer $container
         */
        $container = DIContainer::getInstance();
        $userRepository = $container->get(UserRepository::class);
        $articleRepository = $container->get(ArticleRepository::class);
        $author = $userRepository->findById($commentDecorator->authorId);
        $article = $articleRepository->findById($commentDecorator->articleId);
        return new Comment(
            $author,
            $article,
            $commentDecorator->text,
        );
    }
}
