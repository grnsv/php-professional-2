<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Enums\Argument;
use App\Http\ErrorResponse;
use App\Http\SuccessfulResponse;
use App\Exceptions\HttpException;
use App\Factories\EntityManagerFactory;
use App\Commands\CreateCommentCommandHandler;
use App\Repositories\CommentRepositoryInterface;

class CreateComment implements ActionInterface
{
    public function __construct(
        private ?CommentRepositoryInterface $commentRepository = null,
        private ?CreateCommentCommandHandler $createCommentCommandHandler = null
    ) {
        $this->createCommentCommandHandler = $this->createCommentCommandHandler ?? new CreateCommentCommandHandler($this->commentRepository);
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
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
            'id' => $entity->getId(),
        ]);
    }
}
