<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Http\ErrorResponse;
use App\Http\SuccessfulResponse;
use App\Exceptions\HttpException;
use App\Commands\DeleteEntityCommand;
use App\Commands\DeleteArticleCommandHandler;
use App\Repositories\ArticleRepositoryInterface;

class DeleteArticle implements ActionInterface
{
    public function __construct(
        private ?ArticleRepositoryInterface $articleRepository = null,
        private ?DeleteArticleCommandHandler $deleteArticleCommandHandler = null
    ) {
        $this->deleteArticleCommandHandler = $this->deleteArticleCommandHandler ?? new DeleteArticleCommandHandler($this->articleRepository);
    }

    public function handle(Request $request): Response
    {
        try {
            $id = $request->query('id');
            $this->deleteArticleCommandHandler->handle(new DeleteEntityCommand($id));
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
            'id' => $id,
        ]);
    }
}
