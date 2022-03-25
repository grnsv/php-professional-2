<?php

namespace App\Decorator;

use App\Enums\Article;
use App\Exceptions\CommandException;
use App\Exceptions\ArgumentException;

class ArticleDecorator extends Decorator implements DecoratorInterface
{
    public ?int $id = null;
    public int $authorId;
    public string $title;
    public string $text;

    /**
     * @throws ArgumentException
     * @throws CommandException
     */
    public function __construct(array $arguments)
    {
        parent::__construct($arguments);
        $articleFieldData = $this->getFieldData();

        $this->id = $articleFieldData->get(Article::ID->value) ?? null;
        $this->authorId = $articleFieldData->get(Article::AUTHOR_ID->value);
        $this->title = $articleFieldData->get(Article::TITLE->value);
        $this->text = $articleFieldData->get(Article::TEXT->value);
    }

    public function getRequiredFields(): array
    {
        return Article::getRequiredFields();
    }
}
