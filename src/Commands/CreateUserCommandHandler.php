<?php

namespace App\Commands;

use App\Drivers\Connection;
use App\Entities\User\User;
use Psr\Log\LoggerInterface;
use App\Entities\User\UserInterface;
use App\Exceptions\UserEmailExistsException;
use App\Repositories\UserRepositoryInterface;

class CreateUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private Connection $connection,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws UserEmailExistsException
     * @param CreateEntityCommand $command
     */
    public function handle(CommandInterface $command): void
    {
        $this->logger->info("Create user command started");

        /**
         * @var User $user
         */
        $user = $command->getEntity();
        $email = $user->getEmail();

        if (!$this->userRepository->isUserExists($email)) {
            try {
                $this->connection->beginTransaction();
                $this->connection->prepare($this->getSQL())->execute(
                    [
                        ':firstName' => $user->getFirstName(),
                        ':lastName' => $user->getLastName(),
                        ':email' => $email,
                        ':password' => $user->setPassword($user->getPassword()),
                    ]
                );

                $this->connection->commit();
            } catch (\PDOException $e) {
                $this->connection->rollback();
                print "Error!: " . $e->getMessage() . PHP_EOL;
            }
            $this->logger->info("User created email: $email");
        } else {
            $this->logger->warning("User already exists: $email");
            throw new UserEmailExistsException();
        }
    }

    public function getSQL(): string
    {
        return "INSERT INTO users (first_name, last_name, email, password) 
        VALUES (:firstName, :lastName, :email, :password)";
    }
}
