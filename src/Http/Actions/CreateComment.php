<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Enums\Argument;
use App\Http\ErrorResponse;
use Psr\Log\LoggerInterface;
use App\Http\SuccessfulResponse;
use App\Exceptions\HttpException;
use App\Factories\EntityManagerFactory;
use App\Commands\CreateCommentCommandHandler;

class CreateComment implements ActionInterface
{
    public function __construct(
        private CreateCommentCommandHandler $createCommentCommandHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $entityMangerFactory = EntityManagerFactory::getInstance();
            $entity = $entityMangerFactory->createEntity(
                Argument::COMMENT->value,
                [
                    'authorId=' . $request->jsonBodyField('authorId'),
                    'articleId=' . $request->jsonBodyField('articleId'),
                    'text=' . $request->jsonBodyField('text'),
                ]
            );
            $entityMangerFactory->getEntityManager()->create($entity);
        } catch (HttpException $e) {
            $this->logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'authorId' => $entity->getAuthor()->getId(),
            'articleId' => $entity->getArticle()->getId(),
        ]);
    }
}
