<?php

namespace App\Decorator;

use App\Decorator\Decorator;
use App\Exceptions\CommandException;
use App\Exceptions\ArgumentException;

class CommentDecorator extends Decorator implements DecoratorInterface
{
    public const ID = 'id';
    public const AUTHOR_ID = 'authorId';
    public const ARTICLE_ID = 'articleId';
    public const TEXT = 'text';

    public ?int $id = null;
    public int $authorId;
    public int $articleId;
    public string $text;

    public const REQUIRED_FIELDS = [
        self::AUTHOR_ID,
        self::ARTICLE_ID,
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
        $this->authorId = $articleFieldData->get(self::AUTHOR_ID);
        $this->articleId = $articleFieldData->get(self::ARTICLE_ID);
        $this->text = $articleFieldData->get(self::TEXT);
    }

    public function getRequiredFields(): array
    {
        return static::REQUIRED_FIELDS;
    }
}
