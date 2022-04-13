<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Http\ErrorResponse;
use Psr\Log\LoggerInterface;
use App\Commands\EntityCommand;
use App\Http\SuccessfulResponse;
use App\Entities\Article\Article;
use App\Commands\CreateArticleCommandHandler;
use App\Http\Auth\TokenAuthenticationInterface;

class CreateArticle implements ActionInterface
{
    public function __construct(
        private CreateArticleCommandHandler $createArticleCommandHandler,
        private TokenAuthenticationInterface $authentication,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $article = new Article(
                $this->authentication->getUser($request),
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );

            $article = $this->createArticleCommandHandler->handle(new EntityCommand($article));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->logger->error($e);
            return new ErrorResponse($message);
        }

        $data = [
            'id' => $article->getId(),
            'authorId' => $article->getAuthor()->getId(),
            'title' => $article->getTitle(),
            'text' => $article->getText(),
        ];

        $this->logger->info('Created new Article', $data);
        return new SuccessfulResponse($data);
    }
}
