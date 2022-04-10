<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Entities\User\User;
use App\Http\ErrorResponse;
use Psr\Log\LoggerInterface;
use App\Http\SuccessfulResponse;
use App\Commands\CreateEntityCommand;
use App\Commands\CreateUserCommandHandler;

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
                $request->jsonBodyField('password'),
            );

            $this->createUserCommandHandler->handle(new CreateEntityCommand($user));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->logger->error($e);
            return new ErrorResponse($message);
        }

        $data = [
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
        ];

        $this->logger->info('Created new User', $data);
        return new SuccessfulResponse($data);
    }
}
