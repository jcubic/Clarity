<?php

require __DIR__ . '/vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;

$mime_types = [
    'css' => 'text/css',
    'js'  => 'application/javascript',
    'svg' => 'image/svg+xml',
    'png' => 'image/png',
    'ico' => 'image/x-icon',
];

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$static = __DIR__ . $uri;
if ($uri !== '/' && file_exists($static) && is_file($static)) {
    $ext = pathinfo($static, PATHINFO_EXTENSION);
    $type = $mime_types[$ext] ?? 'application/octet-stream';
    header('Content-Type: ' . $type);
    readfile($static);
    exit;
}

$app = AppFactory::create();

$twig = Twig::create(__DIR__ . '/templates');
$app->add(TwigMiddleware::create($app, $twig));

$icons = json_decode(file_get_contents(__DIR__ . '/icons.json'), true);

$variants = [
    ['id' => 'canus',          'name' => 'Canus',          'latin' => 'hoary · silver-grey', 'oklch' => '75% 0.005 245', 'light' => false],
    ['id' => 'dark_canus',     'name' => 'Dark Canus',     'latin' => 'dark · charcoal',     'oklch' => '35% 0.005 245', 'light' => true],
    ['id' => 'albus',          'name' => 'Albus',          'latin' => 'white · bright',      'oklch' => '95% 0.005 245', 'light' => false],
    ['id' => 'caeruleus',      'name' => 'Caeruleus',      'latin' => 'azure · sky-blue',    'oklch' => '68% 0.16 235',  'light' => false],
    ['id' => 'lux_caeruleus',  'name' => 'Lux Caeruleus',  'latin' => 'light azure',         'oklch' => '82% 0.10 235',  'light' => false],
    ['id' => 'violaceus',      'name' => 'Violaceus',      'latin' => 'violet · amaranth',   'oklch' => '62% 0.20 340',  'light' => false],
    ['id' => 'lux_violaceus',  'name' => 'Lux Violaceus',  'latin' => 'light violet',        'oklch' => '78% 0.12 340',  'light' => false],
    ['id' => 'viridis',        'name' => 'Viridis',        'latin' => 'green · verdant',     'oklch' => '72% 0.17 145',  'light' => false],
    ['id' => 'luteus',         'name' => 'Luteus',         'latin' => 'saffron · amber',     'oklch' => '80% 0.14 88',   'light' => false],
];

$gallery = [
    [
        'id' => 'cyberpunk', 'name' => 'Cyberpunk', 'author' => '@bob',
        'tagline' => 'Neon pink and electric cyan. Synthwave on every glyph — install: <code>@bob/cyberpunk</code>.',
        'version' => 'v1.0', 'date' => '2026-05', 'featured' => true,
        'cover_icons' => ['terminal', 'code', 'music', 'monitor'],
    ],
    [
        'id' => 'lunaris', 'name' => 'Lunaris', 'author' => '@riverstone',
        'tagline' => 'Cold moonlight on glass. Built for OLED displays after midnight.',
        'version' => 'v1.2', 'date' => '2026-04', 'featured' => false,
        'cover_icons' => ['folder', 'music', 'camera', 'clock'],
    ],
    [
        'id' => 'volcanic', 'name' => 'Volcanic', 'author' => '@firekat',
        'tagline' => 'Magma orange against deep ash. Loud, on purpose.',
        'version' => 'v0.9', 'date' => '2026-05', 'featured' => false,
        'cover_icons' => ['terminal', 'power', 'code', 'disk'],
    ],
    [
        'id' => 'forest', 'name' => 'Forest Glass', 'author' => '@osvald',
        'tagline' => 'Translucent moss with a soft fill. Easy on long sessions.',
        'version' => 'v2.0', 'date' => '2026-04', 'featured' => false,
        'cover_icons' => ['maps', 'weather', 'notes', 'home'],
    ],
    [
        'id' => 'tokyo', 'name' => 'Tokyo Night', 'author' => '@kazuya',
        'tagline' => 'Violet-blue, tuned to the popular Vim/Neovim colorscheme.',
        'version' => 'v1.0', 'date' => '2026-03', 'featured' => false,
        'cover_icons' => ['browser', 'chat', 'video', 'settings'],
    ],
    [
        'id' => 'paper', 'name' => 'Paper Bag', 'author' => '@brownie',
        'tagline' => 'Warm sepia. Pairs with the Solarized GTK theme.',
        'version' => 'v1.4', 'date' => '2026-05', 'featured' => false,
        'cover_icons' => ['doc', 'mail', 'calendar', 'pdf'],
    ],
    [
        'id' => 'inverse', 'name' => 'Inverse', 'author' => '@null',
        'tagline' => 'Pure white on absolute black. For maximum contrast setups.',
        'version' => 'v0.4', 'date' => '2026-05', 'featured' => false,
        'cover_icons' => ['monitor', 'keyboard', 'printer', 'archive'],
    ],
];

$app->get('/', function (Request $request, Response $response) use ($icons, $variants, $gallery) {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'pages/home.html.twig', [
        'icons' => $icons,
        'variants' => $variants,
        'gallery' => $gallery,
    ]);
});

$errorMiddleware = $app->addErrorMiddleware(false, false, false);
$errorMiddleware->setErrorHandler(
    Slim\Exception\HttpNotFoundException::class,
    function (Request $request, Throwable $exception, bool $displayErrorDetails) use ($twig) {
        $response = (new Slim\Psr7\Response())->withStatus(404);
        return $twig->render($response, 'pages/404.html.twig');
    }
);

$app->get('/install', function (Request $request, Response $response) {
    $response = $response->withHeader('Content-Type', 'text/plain');
    $response->getBody()->write("#!/usr/bin/env bash\n# Clarity icon theme installer\necho 'Coming soon'\n");
    return $response;
});

$app->run();
