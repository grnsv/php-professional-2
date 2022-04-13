<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Http\ErrorResponse;
use Psr\Log\LoggerInterface;
use App\Commands\EntityCommand;
use App\Http\SuccessfulResponse;
use App\Entities\Comment\Comment;
use App\Commands\CreateCommentCommandHandler;
use App\Http\Auth\TokenAuthenticationInterface;
use App\Repositories\ArticleRepositoryInterface;

class CreateComment implements ActionInterface
{
    public function __construct(
        private CreateCommentCommandHandler $createCommentCommandHandler,
        private TokenAuthenticationInterface $authentication,
        private ArticleRepositoryInterface $articleRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $comment = new Comment(
                $this->authentication->getUser($request),
                $this->articleRepository->findById($request->jsonBodyField('articleId')),
                $request->jsonBodyField('text'),
            );

            $comment = $this->createCommentCommandHandler->handle(new EntityCommand($comment));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->logger->error($e);
            return new ErrorResponse($message);
        }

        $data = [
            'id' => $comment->getId(),
            'authorId' => $comment->getAuthor()->getId(),
            'articleId' => $comment->getArticle()->getId(),
            'text' => $comment->getText(),
        ];

        $this->logger->info('Created new Comment', $data);
        return new SuccessfulResponse($data);
    }
}
