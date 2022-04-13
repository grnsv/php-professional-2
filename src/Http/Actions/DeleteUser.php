<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Http\ErrorResponse;
use Psr\Log\LoggerInterface;
use App\Commands\EntityCommand;
use App\Http\SuccessfulResponse;
use App\Exceptions\HttpException;
use App\Exceptions\UserNotFoundException;
use App\Commands\DeleteUserCommandHandler;
use App\Repositories\UserRepositoryInterface;

class DeleteUser implements ActionInterface
{
    public function __construct(
        private DeleteUserCommandHandler $deleteUserCommandHandler,
        private UserRepositoryInterface $userRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $id = $request->query('id');
            $user = $this->userRepository->findById($id);
            $this->deleteUserCommandHandler->handle(new EntityCommand($user));
        } catch (HttpException | UserNotFoundException $e) {
            $message = $e->getMessage();
            $this->logger->error($e);
            return new ErrorResponse($message);
        }

        return new SuccessfulResponse([
            'id' => $id,
        ]);
    }
}
