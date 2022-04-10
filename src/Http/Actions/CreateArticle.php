<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Enums\Argument;
use App\Http\ErrorResponse;
use Psr\Log\LoggerInterface;
use App\Http\SuccessfulResponse;
use App\Factories\EntityManagerFactory;
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
            $entityMangerFactory = EntityManagerFactory::getInstance();
            $entity = $entityMangerFactory->createEntity(
                Argument::ARTICLE->value,
                [
                    'authorId=' . $this->authentication->getUser($request)->getId(),
                    'title=' . $request->jsonBodyField('title'),
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
            'title' => $entity->getTitle(),
            'text' => $entity->getText(),
        ];

        $this->logger->info('Created new Article', $data);
        return new SuccessfulResponse($data);
    }
}
