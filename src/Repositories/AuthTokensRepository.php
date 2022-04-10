<?php

namespace App\Repositories;

use App\Entities\User\User;
use App\Entities\Token\AuthToken;
use App\Queries\TokenQueryHandlerInterface;

class AuthTokensRepository implements AuthTokensRepositoryInterface
{
    private array $tokens = [];

    public function __construct(private TokenQueryHandlerInterface $tokenQueryHandler)
    {
        $this->tokens = $this->tokenQueryHandler->handle();
    }

    public function getToken(string $token): ?AuthToken
    {
        return $this->tokens[$token] ?? null;
    }

    public function getTokenByUser(User $user): ?AuthToken
    {
        $userToken = null;

        foreach ($this->tokens as $token) {
            if ($user->getId() === $token->getUser()->getId() && !$token->isExpires()) {
                $userToken = $token;
                break;
            }
        }

        return $userToken;
    }

    public function getTokens(): array
    {
        return $this->tokens;
    }
}
