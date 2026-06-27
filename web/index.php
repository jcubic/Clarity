<?php

require __DIR__ . '/vendor/autoload.php';

use Clarity\Database;
use Clarity\Mailer;
use Clarity\SvgValidator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

foreach (['.env.local', '.env'] as $envFile) {
    if (file_exists(__DIR__ . '/' . $envFile)) {
        Dotenv\Dotenv::createImmutable(__DIR__, $envFile)->safeLoad();
    }
}

function env(string $key, ?string $default = null): ?string {
    return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
}

$debug = env('APP_DEBUG', '0') === '1';

if ($debug) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}

try {
    $db = env('DB_HOST')
        ? Database::connect(env('DB_HOST'), env('DB_NAME'), env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_PORT', '3306'))
        : Database::null();
} catch (\PDOException $e) {
    if ($debug) {
        throw $e;
    }
    $db = Database::null();
}

$mailer = env('RESEND_API_KEY')
    ? Mailer::create(env('RESEND_API_KEY'), env('RESEND_EMAIL', 'Clarity <noreply@clarity.pl.eu.org>'))
    : Mailer::null();

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
$twig->getEnvironment()->addFilter(new \Twig\TwigFilter('with_hash', function ($filename) {
    return $filename . '?v=' . dechex(crc32(file_get_contents(__DIR__ . $filename)));
}));
$app->add(TwigMiddleware::create($app, $twig));

$icons = json_decode(file_get_contents(__DIR__ . '/icons.json'), true);

$variants = [
    [
        'id' => 'canus', 'name' => 'Canus', 'latin' => 'hoary · silver-grey',
        'oklch' => '75% 0.005 245', 'light' => false,
        'sub' => [
            'id' => 'dark_canus', 'name' => 'Dark Canus', 'latin' => 'dark · charcoal',
            'oklch' => '35% 0.005 245', 'light' => true, 'toggle' => 'Dark',
        ],
    ],
    [
        'id' => 'caeruleus', 'name' => 'Caeruleus', 'latin' => 'azure · sky-blue',
        'oklch' => '68% 0.16 235', 'light' => false,
        'sub' => [
            'id' => 'lux_caeruleus', 'name' => 'Lux Caeruleus', 'latin' => 'light azure',
            'oklch' => '82% 0.10 235', 'light' => false, 'toggle' => 'Lux',
        ],
    ],
    [
        'id' => 'violaceus', 'name' => 'Violaceus', 'latin' => 'violet · amaranth',
        'oklch' => '62% 0.20 340', 'light' => false,
        'sub' => [
            'id' => 'lux_violaceus', 'name' => 'Lux Violaceus', 'latin' => 'light violet',
            'oklch' => '78% 0.12 340', 'light' => false, 'toggle' => 'Lux',
        ],
    ],
    ['id' => 'viridis', 'name' => 'Viridis', 'latin' => 'green · verdant', 'oklch' => '72% 0.17 145', 'light' => false],
    ['id' => 'luteus', 'name' => 'Luteus', 'latin' => 'saffron · amber', 'oklch' => '80% 0.14 88', 'light' => false],
    ['id' => 'albus', 'name' => 'Albus', 'latin' => 'white · bright', 'oklch' => '95% 0.005 245', 'light' => false],
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

$app->get('/upload', function (Request $request, Response $response) {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'pages/upload.html.twig');
});

$app->post('/api/validate', function (Request $request, Response $response) {
    $files = $request->getUploadedFiles();
    $svg = $files['svg_file'] ?? null;

    if (!$svg || $svg->getError() !== UPLOAD_ERR_OK) {
        $response->getBody()->write(json_encode(['error' => 'No SVG file uploaded.']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $content = (string) $svg->getStream();
    $validator = new SvgValidator();
    $checks = $validator->validate($content);

    $response->getBody()->write(json_encode(['checks' => $checks]));
    return $response->withHeader('Content-Type', 'application/json');
});

$errorMiddleware = $app->addErrorMiddleware($debug, $debug, $debug);
$errorMiddleware->setErrorHandler(
    Slim\Exception\HttpNotFoundException::class,
    function (Request $request, Throwable $exception, bool $displayErrorDetails) use ($twig) {
        $response = (new Slim\Psr7\Response())->withStatus(404);
        return $twig->render($response, 'pages/404.html.twig');
    }
);

$app->get('/stats', function (Request $request, Response $response) use ($db) {
    $stats = $db->getAllCounters() ?: ['installs' => 0];
    $response->getBody()->write(json_encode($stats));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/install', function (Request $request, Response $response) use ($db) {
    $db->incrementCounter('installs');
    return $response
        ->withHeader('Location', 'https://raw.githubusercontent.com/jcubic/Clarity/wasmer/theme/bin/install.sh')
        ->withStatus(302);
});

$app->run();
