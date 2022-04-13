<?php

namespace App\Commands;

use App\Drivers\Connection;
use App\Entities\User\User;
use Psr\Log\LoggerInterface;
use App\Entities\User\UserInterface;
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
     * @param EntityCommand $command
     */
    public function handle(CommandInterface $command): UserInterface
    {
        $this->logger->info("Create user command started");

        /**
         * @var User $user
         */
        $user = $command->getEntity();
        $email = $user->getEmail();

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

        $data = [
            'id' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
        ];

        $this->logger->info('Created new User', $data);

        return $user->getId() ? $user : $this->userRepository->findById($this->connection->lastInsertId());
    }

    public function getSQL(): string
    {
        return "INSERT INTO users (first_name, last_name, email, password) 
        VALUES (:firstName, :lastName, :email, :password)
        ON CONFLICT (email) DO UPDATE SET
            first_name = :firstName,
            last_name = :lastName
        ";
    }
}
