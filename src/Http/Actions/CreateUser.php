<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Entities\User\User;
use App\Http\ErrorResponse;
use Psr\Log\LoggerInterface;
use App\Http\SuccessfulResponse;
use App\Exceptions\HttpException;
use App\Commands\CreateEntityCommand;
use App\Commands\CreateUserCommandHandler;
use App\Exceptions\UserEmailExistsException;

class CreateUser implements ActionInterface
{
    public function __construct(
        private CreateUserCommandHandler $createUserCommandHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $user = new User(
                $request->jsonBodyField('firstName'),
                $request->jsonBodyField('lastName'),
                $request->jsonBodyField('email'),
            );

            $this->createUserCommandHandler->handle(new CreateEntityCommand($user));
        } catch (HttpException | UserEmailExistsException $e) {
            $this->logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'email' => $user->getEmail(),
        ]);
    }
}
