<?php

namespace App\Decorator;

use App\Enums\Comment;
use App\Exceptions\CommandException;
use App\Exceptions\ArgumentException;

class CommentDecorator extends Decorator implements DecoratorInterface
{
    public ?int $id = null;
    public int $authorId;
    public int $articleId;
    public string $text;

    /**
     * @throws ArgumentException
     * @throws CommandException
     */
    public function __construct(array $arguments)
    {
        parent::__construct($arguments);
        $articleFieldData = $this->getFieldData();

        $this->id = $articleFieldData->get(Comment::ID->value) ?? null;
        $this->authorId = $articleFieldData->get(Comment::AUTHOR_ID->value);
        $this->articleId = $articleFieldData->get(Comment::ARTICLE_ID->value);
        $this->text = $articleFieldData->get(Comment::TEXT->value);
    }

    public function getRequiredFields(): array
    {
        return Comment::getRequiredFields();
    }
}
