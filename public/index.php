<?php

const PROJECT_ROOT = __DIR__ . '/..';

require PROJECT_ROOT . '/vendor/autoload.php';

$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
);

$templates = new \League\Plates\Engine(PROJECT_ROOT.'/views', 'phtml');

# Welcome page
if ('/' === $request->getUri()->getPath()) {
    echo $templates->render('welcome');
    return;
}

session_start();

$router = new \League\Route\Router();

$game = new \Games\Tris\Controller\HttpController($templates);
$response = $game->handle($request);

// send the response to the browser
(new Laminas\HttpHandlerRunner\Emitter\SapiEmitter)->emit($response);