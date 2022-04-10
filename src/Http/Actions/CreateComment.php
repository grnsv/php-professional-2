<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Enums\Argument;
use App\Http\ErrorResponse;
use Psr\Log\LoggerInterface;
use App\Http\SuccessfulResponse;
use App\Factories\EntityManagerFactory;
use App\Commands\CreateCommentCommandHandler;
use App\Http\Auth\TokenAuthenticationInterface;

class CreateComment implements ActionInterface
{
    public function __construct(
        private CreateCommentCommandHandler $createCommentCommandHandler,
        private TokenAuthenticationInterface $authentication,
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
                    'authorId=' . $this->authentication->getUser($request)->getId(),
                    'articleId=' . $request->jsonBodyField('articleId'),
                    'text=' . $request->jsonBodyField('text'),
                ]
            );
            $entityMangerFactory->getEntityManager()->create($entity);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->logger->error($e);
            return new ErrorResponse($message);
        }

        $data = [
            'authorId' => $entity->getAuthor()->getId(),
            'articleId' => $entity->getArticle()->getId(),
            'text' => $entity->getText(),
        ];

        $this->logger->info('Created new Comment', $data);
        return new SuccessfulResponse($data);
    }
}
