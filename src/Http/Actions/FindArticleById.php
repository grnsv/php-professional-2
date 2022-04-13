<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Http\ErrorResponse;
use Psr\Log\LoggerInterface;
use App\Http\SuccessfulResponse;
use App\Entities\Article\Article;
use App\Exceptions\HttpException;
use App\Exceptions\ArticleNotFoundException;
use App\Repositories\ArticleRepositoryInterface;

class FindArticleById implements ActionInterface
{
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
        private LoggerInterface $logger,
    ) {
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
             * @var Article $article
             */
            $article = $this->articleRepository->findById($id);
        } catch (ArticleNotFoundException $e) {
            $this->logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'id' => $article->getId(),
            'title' => $article->getTitle(),
        ]);
    }
}
