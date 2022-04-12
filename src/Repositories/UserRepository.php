<?php

namespace App\Repositories;

use PDO;
use PDOStatement;
use App\Drivers\Connection;
use App\Entities\User\User;
use Psr\Log\LoggerInterface;
use App\Exceptions\UserNotFoundException;

class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    public function __construct(
        Connection $connection,
        private LoggerInterface $logger,
    ) {
        parent::__construct($connection);
    }

    /**
     * @throws UserNotFoundException
     */
    public function findById(int $id): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE id = :id'
        );

        $statement->execute([
            ':id' => (string)$id,
        ]);

        return $this->getUser($statement);
    }

    /**
     * @throws UserNotFoundException
     */
    public function getUserByEmail(string $email): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE email = :email'
        );

        $statement->execute([
            ':email' => $email,
        ]);

        return $this->getUser($statement);
    }

    /**
     * @throws UserNotFoundException
     */
    private function getUser(PDOStatement $statement): User
    {
        $result = $statement->fetch(PDO::FETCH_OBJ);

        if (!$result) {
            $this->logger->error('User not found');
            throw new UserNotFoundException('User not found');
        }

        $user = new User(
            firstName: $result->first_name,
            lastName: $result->last_name,
            email: $result->email,
            password: $result->password,
        );

        $user->setId($result->id);
        return $user;
    }

    public function isExists(int $id): bool
    {
        try {
            $this->findById($id);
        } catch (UserNotFoundException) {
            return false;
        }

        return true;
    }

    public function isUserExists(string $email): bool
    {
        try {
            $this->getUserByEmail($email);
        } catch (UserNotFoundException) {
            return false;
        }

        return true;
    }
}
