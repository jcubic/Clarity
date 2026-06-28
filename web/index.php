<?php

require __DIR__ . '/vendor/autoload.php';

use Clarity\Database;
use Clarity\Mailer;
use Clarity\SvgConverter;
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
$coverIcons = json_decode(file_get_contents(__DIR__ . '/cover-icons.json'), true);

$iconsByName = [];
foreach ($icons as $icon) {
    $iconsByName[$icon['name']] = $icon;
}

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

$app->get('/', function (Request $request, Response $response) use ($icons, $variants, $db, $coverIcons) {
    $view = Twig::fromRequest($request);
    $published = $db->getPublishedThemes();
    return $view->render($response, 'pages/home.html.twig', [
        'icons' => $icons,
        'variants' => $variants,
        'published_themes' => $published,
        'cover_icons' => $coverIcons,
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

$app->get('/api/check-name', function (Request $request, Response $response) use ($db) {
    $params = $request->getQueryParams();
    $name = trim($params['name'] ?? '');
    if (!$name || !$db->isConnected()) {
        $response->getBody()->write(json_encode(['available' => true]));
        return $response->withHeader('Content-Type', 'application/json');
    }
    $taken = $db->isThemeNameTaken($name);
    $response->getBody()->write(json_encode(['available' => !$taken]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/upload', function (Request $request, Response $response) use ($db, $mailer) {
    $view = Twig::fromRequest($request);
    $data = $request->getParsedBody();
    $files = $request->getUploadedFiles();

    if (!empty($data['confirm_email'] ?? '')) {
        return $view->render($response, 'pages/upload-sent.html.twig', [
            'email' => 'your inbox',
        ]);
    }

    $email = trim($data['email'] ?? '');
    $username = trim($data['username'] ?? '');
    $themeName = trim($data['theme_name'] ?? '');
    $description = trim($data['theme_description'] ?? '');
    $version = trim($data['theme_version'] ?? '') ?: 'v1.0';
    $svg = $files['svg_file'] ?? null;

    if (!$svg || $svg->getError() !== UPLOAD_ERR_OK || !$email || !$username || !$themeName) {
        return $view->render($response, 'pages/upload.html.twig', [
            'error' => 'All fields are required.',
        ]);
    }

    if (!preg_match('/^[A-Za-z0-9_\-]{2,32}$/', $themeName)) {
        return $view->render($response, 'pages/upload.html.twig', [
            'error' => 'Invalid theme name. Use 2–32 characters: letters, numbers, hyphens, underscores.',
        ]);
    }

    if (!preg_match('/^[A-Za-z0-9_\-]{2,32}$/', $username)) {
        return $view->render($response, 'pages/upload.html.twig', [
            'error' => 'Invalid username. Use 2–32 characters: letters, numbers, hyphens, underscores.',
        ]);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $view->render($response, 'pages/upload.html.twig', [
            'error' => 'Invalid email address.',
        ]);
    }

    $content = (string) $svg->getStream();
    $validator = new SvgValidator();
    $checks = $validator->validate($content);

    $failed = array_filter($checks, fn($c) => !$c['pass']);
    if (!empty($failed)) {
        return $view->render($response, 'pages/upload.html.twig', [
            'error' => 'SVG validation failed. Please fix the issues and try again.',
        ]);
    }

    if ($db->isConnected()) {
        $existingUsername = $db->getUsernameByEmail($email);
        if ($existingUsername !== null && $existingUsername !== $username) {
            return $view->render($response, 'pages/upload.html.twig', [
                'error' => 'This email is already registered with username "@' . htmlspecialchars($existingUsername) . '".',
            ]);
        }

        if ($existingUsername === null && $db->isUsernameTaken($username)) {
            return $view->render($response, 'pages/upload.html.twig', [
                'error' => 'Username "@' . htmlspecialchars($username) . '" is already taken.',
            ]);
        }

        $ownerEmail = $db->getThemeOwnerEmail($themeName);
        if ($ownerEmail !== null && $ownerEmail !== $email) {
            return $view->render($response, 'pages/upload.html.twig', [
                'error' => 'Theme name "' . htmlspecialchars($themeName) . '" is already taken by another user.',
            ]);
        }

        $converter = new SvgConverter();
        $converted = $converter->convert($content);

        $themeId = $db->createTheme($themeName, $description, $version, $converted);
        $token = bin2hex(random_bytes(32));
        $db->createMagicToken($token, $email, $username, $themeId);

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $baseUrl = $scheme . '://' . $host;

        try {
            $mailer->sendUploadLink($email, $token, $themeName, $baseUrl);
        } catch (\Throwable $e) {
            error_log('Mailer error: ' . $e->getMessage());
        }
    }

    return $view->render($response, 'pages/upload-sent.html.twig', [
        'email' => $email,
    ]);
});

$app->get('/verify', function (Request $request, Response $response) use ($db) {
    $view = Twig::fromRequest($request);
    $params = $request->getQueryParams();
    $token = $params['token'] ?? '';

    if (!$token || !$db->isConnected()) {
        return $view->render($response, 'pages/upload-error.html.twig', [
            'error' => 'Invalid or missing verification token.',
        ]);
    }

    $data = $db->verifyMagicToken($token);
    if (!$data) {
        return $view->render($response, 'pages/upload-error.html.twig', [
            'error' => 'This link has expired or has already been used. Please upload your theme again.',
        ]);
    }

    $userId = $db->createOrGetUser($data['email'], $data['username']);
    $themeName = $db->getThemeName($data['theme_id']) ?? 'your theme';

    $existingOwner = $db->getThemeOwnerEmail($themeName);
    if ($existingOwner !== null && $existingOwner === $data['email']) {
        $pending = $db->getThemeById($data['theme_id']);
        if ($pending !== null) {
            $db->replacePublishedTheme($themeName, $userId, $pending['description'], $pending['version'], $pending['svg_content']);
        }
        $db->deleteTheme($data['theme_id']);
    } else {
        $db->publishTheme($data['theme_id'], $userId);
    }

    return $view->render($response, 'pages/upload-confirmed.html.twig', [
        'theme_name' => $themeName,
        'username' => $data['username'],
    ]);
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
    $stats = [
        'counters' => $db->getAllCounters() ?: ['installs' => 0],
        'themes' => $db->getAllThemes(),
    ];
    $response->getBody()->write(json_encode($stats, JSON_PRETTY_PRINT));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/api/theme/{user}/{name}', function (Request $request, Response $response, array $args) use ($db) {
    $svg = $db->getThemeSvg($args['user'], $args['name']);
    if ($svg === null) {
        return $response->withStatus(404);
    }
    $response->getBody()->write($svg);
    return $response->withHeader('Content-Type', 'image/svg+xml');
});

$app->get('/api/icon/{user}/{theme}/{icon}', function (Request $request, Response $response, array $args) use ($db, $iconsByName) {
    $iconName = $args['icon'];
    if (!isset($iconsByName[$iconName])) {
        return $response->withStatus(404);
    }

    $theme = $db->getThemeSvgWithMeta($args['user'], $args['theme']);
    if ($theme === null) {
        return $response->withStatus(404);
    }

    $rendered = str_replace(
        ['{{PATH}}', '{{TITLE}}'],
        [$iconsByName[$iconName]['d'], $iconName],
        $theme['svg_content']
    );

    $etag = '"' . md5($rendered) . '"';
    $ifNoneMatch = $request->getHeaderLine('If-None-Match');
    if ($ifNoneMatch === $etag) {
        return $response->withStatus(304);
    }

    $response->getBody()->write($rendered);
    return $response
        ->withHeader('Content-Type', 'image/svg+xml')
        ->withHeader('ETag', $etag)
        ->withHeader('Cache-Control', 'public, max-age=3600');
});

$app->get('/install', function (Request $request, Response $response) use ($db) {
    $db->incrementCounter('installs');
    return $response
        ->withHeader('Location', 'https://raw.githubusercontent.com/jcubic/Clarity/wasmer/theme/bin/install.sh')
        ->withStatus(302);
});

$app->run();
