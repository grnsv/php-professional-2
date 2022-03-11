<?php

namespace App\Factories;

use JetBrains\PhpStorm\Pure;
use App\Entities\Article\Article;
use App\Decorator\ArticleDecorator;

final class ArticleFactory implements ArticleFactoryInterface
{
    #[Pure] public function create(ArticleDecorator $articleDecorator): Article
    {
        return new Article(
            $articleDecorator->authorId,
            $articleDecorator->title,
            $articleDecorator->text,
        );
    }
}
