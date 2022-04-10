<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Enums\Argument;
use App\Http\ErrorResponse;
use Psr\Log\LoggerInterface;
use App\Http\SuccessfulResponse;
use App\Factories\EntityManagerFactory;
use App\Commands\CreateLikeCommandHandler;
use App\Http\Auth\TokenAuthenticationInterface;

class CreateLike implements ActionInterface
{
    public function __construct(
        private CreateLikeCommandHandler $createLikeCommandHandler,
        private TokenAuthenticationInterface $authentication,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $entityMangerFactory = EntityManagerFactory::getInstance();
            $entity = $entityMangerFactory->createEntity(
                Argument::LIKE->value,
                [
                    'userId=' . $this->authentication->getUser($request)->getId(),
                    'articleId=' . $request->jsonBodyField('articleId'),
                ]
            );
            $entityMangerFactory->getEntityManager()->create($entity);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->logger->error($e);
            return new ErrorResponse($message);
        }

        $data = [
            'userId' => $entity->getUser()->getId(),
            'articleId' => $entity->getArticle()->getId(),
        ];

        $this->logger->info('Created new Like', $data);
        return new SuccessfulResponse($data);
    }
}
