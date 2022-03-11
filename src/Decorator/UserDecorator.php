<?php

namespace App\Decorator;

use App\Decorator\Decorator;
use App\Exceptions\CommandException;
use App\Exceptions\ArgumentException;

class UserDecorator extends Decorator implements DecoratorInterface
{
    public const ID = 'id';
    public const FIRST_NAME = 'firstName';
    public const LAST_NAME = 'lastName';
    public const EMAIL = 'email';

    public ?int $id = null;
    public string $firstName;
    public string $lastName;
    public string $email;

    public const REQUIRED_FIELDS = [
        self::FIRST_NAME,
        self::LAST_NAME,
        self::EMAIL,
    ];

    /**
     * @throws ArgumentException
     * @throws CommandException
     */
    public function __construct(array $arguments)
    {
        parent::__construct($arguments);
        $userFieldData = $this->getFieldData();

        $this->id = $userFieldData->get(self::ID) ?? null;
        $this->firstName = $userFieldData->get(self::FIRST_NAME) ?? null;
        $this->lastName = $userFieldData->get(self::LAST_NAME) ?? null;
        $this->email = $userFieldData->get(self::EMAIL) ?? null;
    }

    public function getRequiredFields(): array
    {
        return static::REQUIRED_FIELDS;
    }
}
