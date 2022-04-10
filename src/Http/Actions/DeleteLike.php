<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Http\ErrorResponse;
use Psr\Log\LoggerInterface;
use App\Http\SuccessfulResponse;
use App\Exceptions\HttpException;
use App\Commands\DeleteEntityCommand;
use App\Exceptions\LikeNotFoundException;
use App\Commands\DeleteLikeCommandHandler;

class DeleteLike implements ActionInterface
{
    public function __construct(
        private DeleteLikeCommandHandler $deleteLikeCommandHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $id = $request->query('id');
            $this->deleteLikeCommandHandler->handle(new DeleteEntityCommand($id));
        } catch (HttpException | LikeNotFoundException $e) {
            $message = $e->getMessage();
            $this->logger->error($e);
            return new ErrorResponse($message);
        }

        return new SuccessfulResponse([
            'id' => $id,
        ]);
    }
}
