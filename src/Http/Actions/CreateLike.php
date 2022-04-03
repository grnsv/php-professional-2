<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Enums\Argument;
use App\Http\ErrorResponse;
use App\Http\SuccessfulResponse;
use App\Exceptions\HttpException;
use App\Factories\EntityManagerFactory;
use App\Commands\CreateLikeCommandHandler;

class CreateLike implements ActionInterface
{
    public function __construct(private CreateLikeCommandHandler $createLikeCommandHandler)
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $entityMangerFactory = EntityManagerFactory::getInstance();
            $entity = $entityMangerFactory->createEntity(
                Argument::LIKE->value,
                [
                    'userId=' . $request->jsonBodyField('userId'),
                    'articleId=' . $request->jsonBodyField('articleId'),
                ]
            );
            $entityMangerFactory->getEntityManager()->create($entity);
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
            'userId' => $entity->getUser()->getId(),
            'articleId' => $entity->getArticle()->getId(),
        ]);
    }
}