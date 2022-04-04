<?php

namespace App\Commands;

use App\Drivers\Connection;
use App\Entities\User\User;
use App\Exceptions\UserEmailExistsException;
use App\Repositories\UserRepositoryInterface;

class CreateUserCommandHandler implements CommandHandlerInterface
{
    private \PDOStatement|false $stmt;

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private Connection $connection
    ) {
        $this->stmt = $connection->prepare($this->getSQL());
    }

    /**
     * @throws UserEmailExistsException
     * @param CreateEntityCommand $command
     */
    public function handle(CommandInterface $command): void
    {
        /**
         * @var User $user
         */
        $user = $command->getEntity();
        $email = $user->getEmail();

        if (!$this->userRepository->isUserExists($email)) {
            $this->stmt->execute(
                [
                    ':firstName' => $user->getFirstName(),
                    ':lastName' => $user->getLastName(),
                    ':email' => $email,
                ]
            );
        } else {
            throw new UserEmailExistsException();
        }
    }

    public function getSQL(): string
    {
        return "INSERT INTO users (first_name, last_name, email) 
        VALUES (:firstName, :lastName, :email)";
    }
}
