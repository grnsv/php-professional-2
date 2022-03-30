<?php

use App\Http\Request;
use App\Http\ErrorResponse;
use App\Http\Actions\CreateUser;
use App\Http\Actions\DeleteUser;
use App\Exceptions\HttpException;
use App\Http\Actions\FindByEmail;
use App\Http\Actions\CreateArticle;
use App\Http\Actions\CreateComment;
use App\Http\Actions\DeleteArticle;
use App\Http\Actions\DeleteComment;
use App\Http\Actions\FindArticleById;
use App\Http\Actions\FindCommentById;

require_once __DIR__ . '/vendor/autoload.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'GET' => [
        '/user/show' => new FindByEmail(),
        '/article/show' => new FindArticleById(),
        '/comment/show' => new FindCommentById(),
    ],
    'POST' => [
        '/user/create' => new CreateUser(),
        '/article/create' => new CreateArticle(),
        '/comment/create' => new CreateComment(),
    ],
    'DELETE' => [
        '/user' => new DeleteUser(),
        '/article' => new DeleteArticle(),
        '/comment' => new DeleteComment(),
    ],
];

if (!array_key_exists($method, $routes)) {
    (new ErrorResponse('Not found'))->send();
    return;
}

if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Not found'))->send();
    return;
}

$action = $routes[$method][$path];

try {
    $response = $action->handle($request);
} catch (Exception $e) {
    (new ErrorResponse($e->getMessage()))->send();
}

$response->send();
