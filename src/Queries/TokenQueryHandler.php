<?php

namespace App\Queries;

use PDO;
use Exception;
use PDOException;
use DateTimeImmutable;
use App\Drivers\Connection;
use App\Entities\Token\AuthToken;
use App\Repositories\UserRepositoryInterface;
use App\Exceptions\AuthTokensRepositoryException;

class TokenQueryHandler implements TokenQueryHandlerInterface
{
    public function __construct(
        private Connection $connection,
        private UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * @return AuthToken[]
     * @throws AuthTokensRepositoryException
     */
    public function handle(): array
    {
        try {
            $statement = $this->connection->prepare($this->getSQL());
            $statement->execute();

            $tokensData = $statement->fetchAll(PDO::FETCH_OBJ);

            try {
                $tokens = [];

                foreach ($tokensData as $tokenData) {
                    $tokens[$tokenData->token] = new AuthToken(
                        $tokenData->token,
                        $this->userRepository->get($tokenData->user_id),
                        new DateTimeImmutable($tokenData->expires_on)
                    );
                }

                return $tokens;
            } catch (Exception $e) {
                throw new AuthTokensRepositoryException(
                    $e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }
        } catch (PDOException $e) {
            throw new AuthTokensRepositoryException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    public function getSQL(): string
    {
        return "SELECT * FROM tokens";
    }
}
