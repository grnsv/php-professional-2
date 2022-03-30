<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Enums\Argument;
use App\Http\ErrorResponse;
use App\Http\SuccessfulResponse;
use App\Exceptions\HttpException;
use App\Factories\EntityManagerFactory;
use App\Commands\CreateArticleCommandHandler;
use App\Repositories\ArticleRepositoryInterface;

class CreateArticle implements ActionInterface
{
    public function __construct(
        private ?ArticleRepositoryInterface $articleRepository = null,
        private ?CreateArticleCommandHandler $createArticleCommandHandler = null
    ) {
        $this->createArticleCommandHandler = $this->createArticleCommandHandler ?? new CreateArticleCommandHandler($this->articleRepository);
    }

    public function handle(Request $request): Response
    {
        try {
            $entityMangerFactory = EntityManagerFactory::getInstance();
            $entity = $entityMangerFactory->createEntity(
                Argument::ARTICLE->value,
                [
                    'authorId=' . $request->jsonBodyField('authorId'),
                    'title=' . $request->jsonBodyField('title'),
                    'text=' . $request->jsonBodyField('text'),
                ]
            );
            $entityMangerFactory->getEntityManager()->create($entity);
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
            'id' => $entity->getId(),
        ]);
    }
}
