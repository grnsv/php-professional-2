<?php

namespace App\Factories;

use App\Enums\Argument;
use JetBrains\PhpStorm\Pure;
use App\Decorator\LikeDecorator;
use App\Decorator\UserDecorator;
use App\Entities\EntityInterface;
use App\Exceptions\MatchException;
use App\Decorator\ArticleDecorator;
use App\Decorator\CommentDecorator;
use App\Exceptions\CommandException;
use App\Exceptions\ArgumentException;

class EntityFactory implements EntityFactoryInterface
{
    private ?UserFactoryInterface $userFactory;
    private ?ArticleFactoryInterface $articleFactory;
    private ?CommentFactoryInterface $commentFactory;
    private ?LikeFactoryInterface $likeFactory;

    #[Pure] public function __construct(
        UserFactoryInterface $userFactory = null,
        ArticleFactoryInterface $articleFactory = null,
        CommentFactoryInterface $commentFactory = null,
        LikeFactoryInterface $likeFactory = null,
    ) {
        $this->userFactory = $userFactory ?? new UserFactory();
        $this->articleFactory = $articleFactory ?? new ArticleFactory();
        $this->commentFactory = $commentFactory ?? new CommentFactory();
        $this->likeFactory = $likeFactory ?? new LikeFactory();
    }

    /**
     * @throws MatchException
     * @throws CommandException
     * @throws ArgumentException
     */
    public function create(string $entityType, array $arguments): EntityInterface
    {
        return match ($entityType) {
            Argument::USER->value => $this->userFactory->create(new UserDecorator($arguments)),
            Argument::ARTICLE->value => $this->articleFactory->create(new ArticleDecorator($arguments)),
            Argument::COMMENT->value => $this->commentFactory->create(new CommentDecorator($arguments)),
            Argument::LIKE->value => $this->likeFactory->create(new LikeDecorator($arguments)),
            default => throw new MatchException(
                sprintf(
                    "Аргумент должен содержать одно из перечисленных значений: '%s'.",
                    implode("', '", array_map(fn (Argument $argument) => $argument->value, Argument::ARGUMENTS))
                )
            )
        };
    }
}
