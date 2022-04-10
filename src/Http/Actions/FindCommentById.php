<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Http\ErrorResponse;
use Psr\Log\LoggerInterface;
use App\Http\SuccessfulResponse;
use App\Entities\Comment\Comment;
use App\Exceptions\HttpException;
use App\Factories\EntityManagerFactory;
use App\Exceptions\CommentNotFoundException;
use App\Repositories\CommentRepositoryInterface;

class FindCommentById implements ActionInterface
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private LoggerInterface $logger,
    ) {
        $this->commentRepository = $this->commentRepository ?? EntityManagerFactory::getInstance()->getRepository(Comment::class);
    }

    public function handle(Request $request): Response
    {
        try {
            $id = $request->query('id');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            /**
             * @var Comment $comment
             */
            $comment = $this->commentRepository->get($id);
        } catch (CommentNotFoundException $e) {
            $this->logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'id' => $comment->getId(),
            'text' => $comment->getText(),
        ]);
    }
}
