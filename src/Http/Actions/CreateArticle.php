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

class CreateArticle implements ActionInterface
{
    public function __construct(private CreateArticleCommandHandler $createArticleCommandHandler)
    {
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
            'authorId' => $entity->getAuthor()->getId(),
            'title' => $entity->getTitle(),
        ]);
    }
}
