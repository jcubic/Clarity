<?php

require __DIR__ . '/vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

$app = AppFactory::create();

$twig = Twig::create(__DIR__ . '/templates');
$app->add(TwigMiddleware::create($app, $twig));

$app->get('/', function ($request, $response) {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'home.html.twig', [
        'slim_version' => \Slim\App::VERSION,
        'twig_version' => \Twig\Environment::VERSION,
        'php_version' => phpversion(),
    ]);
});

$app->run();
