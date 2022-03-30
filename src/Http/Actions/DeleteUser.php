<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Http\ErrorResponse;
use App\Http\SuccessfulResponse;
use App\Exceptions\HttpException;
use App\Commands\DeleteEntityCommand;
use App\Commands\DeleteUserCommandHandler;
use App\Repositories\UserRepositoryInterface;

class DeleteUser implements ActionInterface
{
    public function __construct(
        private ?UserRepositoryInterface $userRepository = null,
        private ?DeleteUserCommandHandler $deleteUserCommandHandler = null
    ) {
        $this->deleteUserCommandHandler = $this->deleteUserCommandHandler ?? new DeleteUserCommandHandler($this->userRepository);
    }

    public function handle(Request $request): Response
    {
        try {
            $id = $request->query('id');
            $this->deleteUserCommandHandler->handle(new DeleteEntityCommand($id));
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
            'id' => $id,
        ]);
    }
}
