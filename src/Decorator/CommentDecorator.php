<?php

namespace App\Decorator;

use App\Entities\User\User;
use App\Decorator\Decorator;
use App\Entities\Article\Article;
use App\Exceptions\CommandException;
use App\Exceptions\ArgumentException;

class CommentDecorator extends Decorator implements DecoratorInterface
{
    public const ID = 'id';
    public const AUTHOR = 'author';
    public const ARTICLE = 'article';
    public const TEXT = 'text';

    public ?int $id = null;
    public User $author;
    public Article $article;
    public string $text;

    public const REQUIRED_FIELDS = [
        self::AUTHOR,
        self::ARTICLE,
        self::TEXT,
    ];

    /**
     * @throws ArgumentException
     * @throws CommandException
     */
    public function __construct(array $arguments)
    {
        parent::__construct($arguments);
        $articleFieldData = $this->getFieldData();

        $this->id = $articleFieldData->get(self::ID) ?? null;
        $this->author = $articleFieldData->get(self::AUTHOR) ?? null;
        $this->title = $articleFieldData->get(self::ARTICLE) ?? null;
        $this->text = $articleFieldData->get(self::TEXT) ?? null;
    }

    public function getRequiredFields(): array
    {
        return static::REQUIRED_FIELDS;
    }
}
