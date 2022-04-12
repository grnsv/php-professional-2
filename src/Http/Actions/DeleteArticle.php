<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Http\ErrorResponse;
use Psr\Log\LoggerInterface;
use App\Commands\EntityCommand;
use App\Http\SuccessfulResponse;
use App\Exceptions\HttpException;
use App\Exceptions\ArticleNotFoundException;
use App\Commands\DeleteArticleCommandHandler;
use App\Repositories\ArticleRepositoryInterface;

class DeleteArticle implements ActionInterface
{
    public function __construct(
        private DeleteArticleCommandHandler $deleteArticleCommandHandler,
        private ArticleRepositoryInterface $articleRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $id = $request->query('id');
            $article = $this->articleRepository->findById($id);
            $this->deleteArticleCommandHandler->handle(new EntityCommand($article));
        } catch (HttpException | ArticleNotFoundException $e) {
            $message = $e->getMessage();
            $this->logger->error($e);
            return new ErrorResponse($message);
        }

        return new SuccessfulResponse([
            'id' => $id,
        ]);
    }
}
