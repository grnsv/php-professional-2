<?php

use App\Http\Request;
use App\Http\ErrorResponse;
use App\Http\Actions\CreateLike;
use App\Http\Actions\CreateUser;
use App\Http\Actions\DeleteLike;
use App\Http\Actions\DeleteUser;
use App\Exceptions\HttpException;
use App\Http\Actions\FindByEmail;
use App\Http\Actions\CreateArticle;
use App\Http\Actions\CreateComment;
use App\Http\Actions\DeleteArticle;
use App\Http\Actions\DeleteComment;
use App\Http\Actions\FindArticleById;
use App\Http\Actions\FindCommentById;

$container = require __DIR__ . '/bootstrap.php';
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
        '/user/show'    => FindByEmail::class,
        '/article/show' => FindArticleById::class,
        '/comment/show' => FindCommentById::class,
    ],
    'POST' => [
        '/user/create'    => CreateUser::class,
        '/article/create' => CreateArticle::class,
        '/comment/create' => CreateComment::class,
        '/like/create'    => CreateLike::class,
    ],
    'DELETE' => [
        '/user'    => DeleteUser::class,
        '/article' => DeleteArticle::class,
        '/comment' => DeleteComment::class,
        '/like'    => DeleteLike::class,
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

$actionClassName = $routes[$method][$path];
$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);
} catch (Exception $e) {
    (new ErrorResponse($e->getMessage()))->send();
}

$response->send();
