<?php

namespace App\Decorator;

use App\Enums\Like;
use App\Exceptions\CommandException;
use App\Exceptions\ArgumentException;

class LikeDecorator extends Decorator implements DecoratorInterface
{
    public ?int $id = null;
    public int $userId;
    public int $articleId;

    /**
     * @throws ArgumentException
     * @throws CommandException
     */
    public function __construct(array $arguments)
    {
        parent::__construct($arguments);
        $articleFieldData = $this->getFieldData();

        $this->id = $articleFieldData->get(Like::ID->value) ?? null;
        $this->userId = $articleFieldData->get(Like::USER_ID->value);
        $this->articleId = $articleFieldData->get(Like::ARTICLE_ID->value);
    }

    public function getRequiredFields(): array
    {
        return Like::getRequiredFields();
    }
}
