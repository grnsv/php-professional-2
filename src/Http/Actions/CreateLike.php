<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Entities\Like\Like;
use App\Http\ErrorResponse;
use Psr\Log\LoggerInterface;
use App\Commands\EntityCommand;
use App\Http\SuccessfulResponse;
use App\Commands\CreateLikeCommandHandler;
use App\Http\Auth\TokenAuthenticationInterface;
use App\Repositories\ArticleRepositoryInterface;

class CreateLike implements ActionInterface
{
    public function __construct(
        private CreateLikeCommandHandler $createLikeCommandHandler,
        private TokenAuthenticationInterface $authentication,
        private ArticleRepositoryInterface $articleRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $like = new Like(
                $this->authentication->getUser($request),
                $this->articleRepository->findById($request->jsonBodyField('articleId')),
            );

            $like = $this->createLikeCommandHandler->handle(new EntityCommand($like));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->logger->error($e);
            return new ErrorResponse($message);
        }

        $data = [
            'id' => $like->getId(),
            'userId' => $like->getUser()->getId(),
            'articleId' => $like->getArticle()->getId(),
        ];

        $this->logger->info('Created new Like', $data);
        return new SuccessfulResponse($data);
    }
}
