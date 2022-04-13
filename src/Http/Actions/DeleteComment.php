<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Http\ErrorResponse;
use Psr\Log\LoggerInterface;
use App\Commands\EntityCommand;
use App\Http\SuccessfulResponse;
use App\Exceptions\HttpException;
use App\Exceptions\CommentNotFoundException;
use App\Commands\DeleteCommentCommandHandler;
use App\Repositories\CommentRepositoryInterface;

class DeleteComment implements ActionInterface
{
    public function __construct(
        private DeleteCommentCommandHandler $deleteCommentCommandHandler,
        private CommentRepositoryInterface $commentRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $id = $request->query('id');
            $comment = $this->commentRepository->findById($id);
            $this->deleteCommentCommandHandler->handle(new EntityCommand($comment));
        } catch (HttpException | CommentNotFoundException $e) {
            $message = $e->getMessage();
            $this->logger->error($e);
            return new ErrorResponse($message);
        }

        return new SuccessfulResponse([
            'id' => $id,
        ]);
    }
}
