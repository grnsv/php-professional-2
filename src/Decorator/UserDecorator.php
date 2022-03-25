<?php

namespace App\Decorator;

use App\Enums\User;
use App\Exceptions\CommandException;
use App\Exceptions\ArgumentException;

class UserDecorator extends Decorator implements DecoratorInterface
{
    public ?int $id = null;
    public string $firstName;
    public string $lastName;
    public string $email;

    /**
     * @throws ArgumentException
     * @throws CommandException
     */
    public function __construct(array $arguments)
    {
        parent::__construct($arguments);
        $userFieldData = $this->getFieldData();

        $this->id = $userFieldData->get(User::ID->value) ?? null;
        $this->firstName = $userFieldData->get(User::FIRST_NAME->value) ?? null;
        $this->lastName = $userFieldData->get(User::LAST_NAME->value) ?? null;
        $this->email = $userFieldData->get(User::EMAIL->value) ?? null;
    }

    public function getRequiredFields(): array
    {
        return User::getRequiredFields();
    }
}
