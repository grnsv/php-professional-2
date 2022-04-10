<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use DateTimeImmutable;
use App\Http\ErrorResponse;
use App\Http\TokenResponse;
use App\Entities\Token\AuthToken;
use App\Exceptions\AuthException;
use App\Commands\TokenCommandHandlerInterface;
use App\Http\Auth\PasswordAuthenticationInterface;
use App\Repositories\AuthTokensRepositoryInterface;

class LogIn implements ActionInterface
{
    public function __construct(
        private PasswordAuthenticationInterface $passwordAuthentication,
        private TokenCommandHandlerInterface $tokenCommandHandler,
        private AuthTokensRepositoryInterface $authTokensRepository,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function handle(Request $request): Response
    {
        try {
            $user = $this->passwordAuthentication->getUser($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        if (!$authToken = $this->authTokensRepository->getTokenByUser($user)) {
            $authToken = new AuthToken(
                bin2hex(random_bytes(40)),
                $user,
                (new DateTimeImmutable())->modify('+1 day'),
            );
        }

        $this->tokenCommandHandler->handle($authToken);
        return new TokenResponse($authToken);
    }
}
