<?php

namespace App\Decorator;

use App\Decorator\Decorator;
use App\Exceptions\CommandException;
use App\Exceptions\ArgumentException;

class ArticleDecorator extends Decorator implements DecoratorInterface
{
    public const ID = 'id';
    public const AUTHOR_ID = 'authorId';
    public const TITLE = 'title';
    public const TEXT = 'text';

    public ?int $id = null;
    public int $authorId;
    public string $title;
    public string $text;

    public const REQUIRED_FIELDS = [
        self::AUTHOR_ID,
        self::TITLE,
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
        $this->authorId = $articleFieldData->get(self::AUTHOR_ID) ?? null;
        $this->title = $articleFieldData->get(self::TITLE) ?? null;
        $this->text = $articleFieldData->get(self::TEXT) ?? null;
    }

    public function getRequiredFields(): array
    {
        return static::REQUIRED_FIELDS;
    }
}
