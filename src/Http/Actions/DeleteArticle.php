<?php

namespace App\Http\Actions;

use App\Http\Request;
use App\Http\Response;
use App\Http\ErrorResponse;
use App\Http\SuccessfulResponse;
use App\Exceptions\HttpException;
use App\Commands\DeleteEntityCommand;
use App\Exceptions\ArticleNotFoundException;
use App\Commands\DeleteArticleCommandHandler;

class DeleteArticle implements ActionInterface
{
    public function __construct(private DeleteArticleCommandHandler $deleteArticleCommandHandler)
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $id = $request->query('id');
            $this->deleteArticleCommandHandler->handle(new DeleteEntityCommand($id));
        } catch (HttpException | ArticleNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
            'id' => $id,
        ]);
    }
}
