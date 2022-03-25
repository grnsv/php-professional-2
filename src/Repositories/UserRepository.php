<?php

namespace App\Repositories;

use PDO;
use PDOStatement;
use App\Entities\User\User;
use App\Exceptions\UserNotFoundException;

class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    /**
     * @throws UserNotFoundException
     */
    public function get(int $id): User
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
            throw new UserNotFoundException('User not found');
        }

        $user = new User(
            $result->first_name,
            $result->last_name,
            $result->email
        );
        $user->setId($result->id);
        return $user;
    }
}
