<?php

use App\Http\Request;
use App\Http\ErrorResponse;
use Psr\Log\LoggerInterface;
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

$logger = $container->get(LoggerInterface::class);

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

try {
    $path = $request->path();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
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

if (
    !array_key_exists($method, $routes)
    || !array_key_exists($path, $routes[$method])
) {
    $logger->info(sprintf('Клиент с IP-адресом :%s пытался получить несуществующий роут', $_SERVER['REMOTE_ADDR']));

    $message = "Route not found: $method $path";
    $logger->notice($message);
    (new ErrorResponse($message))->send();
    return;
}

$actionClassName = $routes[$method][$path];

try {
    $action = $container->get($actionClassName);
    $response = $action->handle($request);
} catch (Exception $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse($e->getMessage()))->send();
}

$response->send();
