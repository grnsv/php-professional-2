<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Http\ErrorResponse;
use App\Http\SuccessfulResponse;
use App\Exceptions\HttpException;
use App\Commands\DeleteEntityCommand;
use App\Commands\DeleteCommentCommandHandler;
use App\Repositories\CommentRepositoryInterface;

class DeleteComment implements ActionInterface
{
    public function __construct(
        private ?CommentRepositoryInterface $commentRepository = null,
        private ?DeleteCommentCommandHandler $deleteCommentCommandHandler = null
    ) {
        $this->deleteCommentCommandHandler = $this->deleteCommentCommandHandler ?? new DeleteCommentCommandHandler($this->commentRepository);
    }

    public function handle(Request $request): Response
    {
        try {
            $id = $request->query('id');
            $this->deleteCommentCommandHandler->handle(new DeleteEntityCommand($id));
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
            'id' => $id,
        ]);
    }
}
