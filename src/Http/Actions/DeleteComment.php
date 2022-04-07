<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Http\ErrorResponse;
use Psr\Log\LoggerInterface;
use App\Http\SuccessfulResponse;
use App\Exceptions\HttpException;
use App\Commands\DeleteEntityCommand;
use App\Exceptions\CommentNotFoundException;
use App\Commands\DeleteCommentCommandHandler;

class DeleteComment implements ActionInterface
{
    public function __construct(
        private DeleteCommentCommandHandler $deleteCommentCommandHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $id = $request->query('id');
            $this->deleteCommentCommandHandler->handle(new DeleteEntityCommand($id));
        } catch (HttpException | CommentNotFoundException $e) {
            $this->logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'id' => $id,
        ]);
    }
}
