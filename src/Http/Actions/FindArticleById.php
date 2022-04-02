<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Http\ErrorResponse;
use App\Http\SuccessfulResponse;
use App\Entities\Article\Article;
use App\Exceptions\HttpException;
use App\Factories\EntityManagerFactory;
use App\Exceptions\ArticleNotFoundException;
use App\Repositories\ArticleRepositoryInterface;

class FindArticleById implements ActionInterface
{
    public function __construct(private ?ArticleRepositoryInterface $articleRepository = null)
    {
        $this->articleRepository = $this->articleRepository ?? EntityManagerFactory::getInstance()->getRepository(Article::class);
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
            $article = $this->articleRepository->get($id);
        } catch (ArticleNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'id' => $article->getId(),
            'title' => $article->getTitle(),
        ]);
    }
}
