<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Http\ErrorResponse;
use App\Http\SuccessfulResponse;
use App\Exceptions\HttpException;
use App\Commands\DeleteEntityCommand;
use App\Exceptions\UserNotFoundException;
use App\Commands\DeleteUserCommandHandler;

class DeleteUser implements ActionInterface
{
    public function __construct(private DeleteUserCommandHandler $deleteUserCommandHandler)
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $id = $request->query('id');
            $this->deleteUserCommandHandler->handle(new DeleteEntityCommand($id));
        } catch (HttpException | UserNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
            'id' => $id,
        ]);
    }
}
