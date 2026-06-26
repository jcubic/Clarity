<?php

require __DIR__ . '/vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app = AppFactory::create();

$twig = Twig::create(__DIR__ . '/templates');
$app->add(TwigMiddleware::create($app, $twig));

$icons = [
    ['id' => 'folder',     'name' => 'folder',    'label' => 'folder'],
    ['id' => 'doc',        'name' => 'document',  'label' => 'document'],
    ['id' => 'image',      'name' => 'image',     'label' => 'image'],
    ['id' => 'monitor',    'name' => 'monitor',   'label' => 'monitor'],
    ['id' => 'terminal',   'name' => 'terminal',  'label' => 'terminal'],
    ['id' => 'settings',   'name' => 'settings',  'label' => 'settings'],
    ['id' => 'calculator', 'name' => 'calc',      'label' => 'calculator'],
    ['id' => 'calendar',   'name' => 'calendar',  'label' => 'calendar'],
    ['id' => 'mail',       'name' => 'mail',      'label' => 'mail'],
    ['id' => 'music',      'name' => 'music',     'label' => 'music'],
    ['id' => 'video',      'name' => 'video',     'label' => 'video'],
    ['id' => 'camera',     'name' => 'camera',    'label' => 'camera'],
    ['id' => 'maps',       'name' => 'maps',      'label' => 'maps'],
    ['id' => 'weather',    'name' => 'weather',   'label' => 'weather'],
    ['id' => 'notes',      'name' => 'notes',     'label' => 'notes'],
    ['id' => 'clock',      'name' => 'clock',     'label' => 'clock'],
    ['id' => 'contacts',   'name' => 'contacts',  'label' => 'contacts'],
    ['id' => 'chat',       'name' => 'chat',      'label' => 'chat'],
    ['id' => 'sheet',      'name' => 'sheet',     'label' => 'spreadsheet'],
    ['id' => 'slides',     'name' => 'slides',    'label' => 'slides'],
    ['id' => 'pdf',        'name' => 'pdf',       'label' => 'pdf'],
    ['id' => 'code',       'name' => 'code',      'label' => 'code'],
    ['id' => 'archive',    'name' => 'archive',   'label' => 'archive'],
    ['id' => 'disk',       'name' => 'disk',      'label' => 'disk'],
    ['id' => 'activity',   'name' => 'activity',  'label' => 'activity'],
    ['id' => 'package',    'name' => 'software',  'label' => 'package'],
    ['id' => 'bluetooth',  'name' => 'bluetooth', 'label' => 'bluetooth'],
    ['id' => 'wifi',       'name' => 'wifi',      'label' => 'wifi'],
    ['id' => 'network',    'name' => 'network',   'label' => 'network'],
    ['id' => 'power',      'name' => 'power',     'label' => 'power'],
    ['id' => 'display',    'name' => 'display',   'label' => 'display'],
    ['id' => 'volume',     'name' => 'volume',    'label' => 'volume'],
    ['id' => 'keyboard',   'name' => 'keyboard',  'label' => 'keyboard'],
    ['id' => 'mouse',      'name' => 'mouse',     'label' => 'mouse'],
    ['id' => 'printer',    'name' => 'printer',   'label' => 'printer'],
    ['id' => 'screenshot', 'name' => 'snap',      'label' => 'screenshot'],
    ['id' => 'trash',      'name' => 'trash',     'label' => 'trash'],
    ['id' => 'dl-folder',  'name' => 'downloads', 'label' => 'downloads'],
    ['id' => 'home',       'name' => 'home',      'label' => 'home'],
    ['id' => 'browser',    'name' => 'browser',   'label' => 'browser'],
];

$variants = [
    ['id' => 'canus',     'name' => 'Canus',     'latin' => 'hoary · silver-grey', 'oklch' => '75% 0.005 245'],
    ['id' => 'caeruleus', 'name' => 'Caeruleus', 'latin' => 'azure · sky-blue',    'oklch' => '68% 0.16 235'],
    ['id' => 'violaceus', 'name' => 'Violaceus', 'latin' => 'violet · amaranth',   'oklch' => '62% 0.20 340'],
    ['id' => 'viridis',   'name' => 'Viridis',   'latin' => 'green · verdant',     'oklch' => '72% 0.17 145'],
    ['id' => 'luteus',    'name' => 'Luteus',    'latin' => 'saffron · amber',     'oklch' => '80% 0.14 88'],
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

// Static file routes (Wasmer phpix routes everything through index.php)
$app->get('/css/{file:.*}', function (Request $request, Response $response, array $args) {
    $file = __DIR__ . '/public/css/' . $args['file'];
    if (!file_exists($file)) {
        return $response->withStatus(404);
    }
    $response = $response->withHeader('Content-Type', 'text/css');
    $response->getBody()->write(file_get_contents($file));
    return $response;
});

$app->get('/js/{file:.*}', function (Request $request, Response $response, array $args) {
    $file = __DIR__ . '/public/js/' . $args['file'];
    if (!file_exists($file)) {
        return $response->withStatus(404);
    }
    $response = $response->withHeader('Content-Type', 'application/javascript');
    $response->getBody()->write(file_get_contents($file));
    return $response;
});

$app->get('/', function (Request $request, Response $response) use ($icons, $variants, $gallery) {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'pages/home.html.twig', [
        'icons' => $icons,
        'variants' => $variants,
        'gallery' => $gallery,
    ]);
});

$app->get('/debug', function (Request $request, Response $response) {
    $info = [
        '__DIR__' => __DIR__,
        'cwd' => getcwd(),
        'public_exists' => file_exists(__DIR__ . '/public'),
        'css_dir_exists' => file_exists(__DIR__ . '/public/css'),
        'css_file_exists' => file_exists(__DIR__ . '/public/css/style.css'),
        'scandir_root' => scandir(__DIR__),
    ];
    if (file_exists(__DIR__ . '/public')) {
        $info['scandir_public'] = scandir(__DIR__ . '/public');
    }
    $response = $response->withHeader('Content-Type', 'application/json');
    $response->getBody()->write(json_encode($info, JSON_PRETTY_PRINT));
    return $response;
});

$app->get('/install', function (Request $request, Response $response) {
    $response = $response->withHeader('Content-Type', 'text/plain');
    $response->getBody()->write("#!/usr/bin/env bash\n# Clarity icon theme installer\necho 'Coming soon'\n");
    return $response;
});

$app->run();
