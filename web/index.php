<?php

require __DIR__ . '/vendor/autoload.php';

use Clarity\Database;
use Clarity\Mailer;
use Clarity\SvgConverter;
use Clarity\SvgValidator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use WikiZEIT\HTMLMinifier;
use z4kn4fein\SemVer\Version;

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

$jwtSecret = hash('sha256', env('JWT_SECRET', ''), true);

/** @return array{user_id: int, email: string, username: string}|null */
function getAuthUser(Request $request, string $secret): ?array {
    $cookies = $request->getCookieParams();
    $token = $cookies['clarity_auth'] ?? null;
    if (!$token) {
        return null;
    }
    try {
        $payload = (array) JWT::decode($token, new Key($secret, 'HS256'));
        return [
            'user_id' => (int) $payload['uid'],
            'email' => (string) $payload['email'],
            'username' => (string) $payload['sub'],
        ];
    } catch (\Throwable $e) {
        return null;
    }
}

function setAuthCookie(Response $response, string $secret, int $userId, string $email, string $username): Response {
    $payload = [
        'sub' => $username,
        'uid' => $userId,
        'email' => $email,
        'iat' => time(),
        'exp' => time() + 30 * 86400,
    ];
    $jwt = JWT::encode($payload, $secret, 'HS256');
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $cookie = 'clarity_auth=' . $jwt
        . '; Path=/; HttpOnly; SameSite=Lax; Max-Age=' . (30 * 86400)
        . ($secure ? '; Secure' : '');
    return $response->withHeader('Set-Cookie', $cookie);
}

function getIpHash(Request $request): string {
    $ip = $request->getServerParams()['REMOTE_ADDR'] ?? '0.0.0.0';
    $forwarded = $request->getHeaderLine('X-Forwarded-For');
    if ($forwarded) {
        $ip = trim(explode(',', $forwarded)[0]);
    }
    return hash('sha256', $ip . env('JWT_SECRET', ''));
}

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

if (!$debug) {
    $app->add(function (Request $request, \Psr\Http\Server\RequestHandlerInterface $handler) {
        $response = $handler->handle($request);
        $contentType = $response->getHeaderLine('Content-Type');
        if ($contentType !== '' && stripos($contentType, 'text/html') === false) {
            return $response;
        }
        $html = (string) $response->getBody();
        $minifier = new HTMLMinifier();
        $minified = $minifier->run($html);
        $response = $response->withBody(new \Slim\Psr7\Stream(fopen('php://temp', 'r+')));
        $response->getBody()->write($minified);
        return $response;
    });
}

$icons = json_decode(file_get_contents(__DIR__ . '/icons.json'), true);
$coverIcons = json_decode(file_get_contents(__DIR__ . '/cover-icons.json'), true);
$stats = file_exists(__DIR__ . '/icons-stats.json')
    ? json_decode(file_get_contents(__DIR__ . '/icons-stats.json'), true)
    : ['unique_icons' => 0, 'with_symlinks' => 0, 'variants' => 0];

$iconsByName = [];
foreach ($icons as $icon) {
    $iconsByName[$icon['name']] = $icon;
}

$variantData = json_decode(file_get_contents(__DIR__ . '/variants.json'), true);
$variants = [];
$childMap = [];
foreach ($variantData as $v) {
    if (isset($v['parent'])) {
        $childMap[$v['parent']][] = $v;
    }
}
foreach ($variantData as $v) {
    if (isset($v['parent'])) {
        continue;
    }
    $v['subs'] = $childMap[$v['id']] ?? [];
    $variants[] = $v;
}

$app->get('/', function (Request $request, Response $response) use ($icons, $variants, $db, $coverIcons, $stats) {
    $view = Twig::fromRequest($request);
    $published = $db->getPublishedThemes();
    $installs = $db->getCounter('installs');
    return $view->render($response, 'pages/home.html.twig', [
        'icons' => $icons,
        'variants' => $variants,
        'stats' => $stats,
        'published_themes' => $published,
        'cover_icons' => $coverIcons,
        'installs' => $installs,
    ]);
});

$app->get('/theme/{user}/{name}', function (Request $request, Response $response, array $args) use ($db, $icons, $stats, $jwtSecret) {
    $view = Twig::fromRequest($request);
    $theme = $db->getThemeDetail($args['user'], $args['name']);
    if ($theme === null) {
        $response = $response->withStatus(404);
        return $view->render($response, 'pages/404.html.twig');
    }
    $ipHash = getIpHash($request);
    $db->recordThemeView((int) $theme['id'], $ipHash);
    $likeCount = $db->getLikeCount((int) $theme['id']);
    $hasLiked = $db->hasLiked((int) $theme['id'], $ipHash);
    $authUser = getAuthUser($request, $jwtSecret);
    $isOwner = $authUser !== null && $authUser['username'] === $args['user'];
    $authorThemeCount = $db->getUserThemeCount($args['user']);
    $previewIcons = array_slice($icons, 0, 27);
    $featuredIcon = $icons[array_rand($icons)];
    return $view->render($response, 'pages/theme.html.twig', [
        'theme' => $theme,
        'like_count' => $likeCount,
        'has_liked' => $hasLiked,
        'is_owner' => $isOwner,
        'author_theme_count' => $authorThemeCount,
        'preview_icons' => $previewIcons,
        'featured_icon' => $featuredIcon,
        'stats' => $stats,
    ]);
});

$app->get('/theme/{user}/{name}/edit', function (Request $request, Response $response, array $args) use ($db, $jwtSecret) {
    $authUser = getAuthUser($request, $jwtSecret);
    if ($authUser === null || $authUser['username'] !== $args['user']) {
        return $response->withHeader('Location', '/theme/' . $args['user'] . '/' . $args['name'])->withStatus(302);
    }
    $theme = $db->getThemeDetail($args['user'], $args['name']);
    if ($theme === null) {
        $view = Twig::fromRequest($request);
        return $view->render($response->withStatus(404), 'pages/404.html.twig');
    }
    $view = Twig::fromRequest($request);
    return $view->render($response, 'pages/theme-edit.html.twig', [
        'theme' => $theme,
    ]);
});

$app->post('/theme/{user}/{name}/edit', function (Request $request, Response $response, array $args) use ($db, $jwtSecret) {
    $authUser = getAuthUser($request, $jwtSecret);
    if ($authUser === null || $authUser['username'] !== $args['user']) {
        return $response->withHeader('Location', '/theme/' . $args['user'] . '/' . $args['name'])->withStatus(302);
    }
    $themeId = $db->getThemeIdBySlug($args['user'], $args['name']);
    if ($themeId === null) {
        $view = Twig::fromRequest($request);
        return $view->render($response->withStatus(404), 'pages/404.html.twig');
    }
    $body = $request->getParsedBody();
    $description = trim((string) ($body['description'] ?? ''));
    $isDark = isset($body['is_dark']);
    $error = null;
    if (mb_strlen($description) > 200) {
        $error = 'Description must be 200 characters or fewer.';
    }
    if ($error !== null) {
        $theme = $db->getThemeDetail($args['user'], $args['name']);
        $view = Twig::fromRequest($request);
        return $view->render($response, 'pages/theme-edit.html.twig', [
            'theme' => $theme,
            'error' => $error,
            'old' => ['description' => $description, 'is_dark' => $isDark],
        ]);
    }
    $db->updateThemeMetadata($themeId, $description, $isDark);
    return $response->withHeader('Location', '/theme/' . $args['user'] . '/' . $args['name'])->withStatus(302);
});

$app->post('/theme/{user}/{name}/delete', function (Request $request, Response $response, array $args) use ($db, $jwtSecret) {
    $authUser = getAuthUser($request, $jwtSecret);
    if ($authUser === null || $authUser['username'] !== $args['user']) {
        return $response->withHeader('Location', '/theme/' . $args['user'] . '/' . $args['name'])->withStatus(302);
    }
    $themeId = $db->getThemeIdBySlug($args['user'], $args['name']);
    if ($themeId !== null) {
        $db->deleteTheme($themeId);
    }
    return $response->withHeader('Location', '/#gallery')->withStatus(302);
});

$app->get('/upload', function (Request $request, Response $response) use ($jwtSecret, $iconsByName, $coverIcons) {
    $view = Twig::fromRequest($request);
    $user = getAuthUser($request, $jwtSecret);
    $previewIcons = array_filter(array_map(
        fn($name) => $iconsByName[$name] ?? null,
        $coverIcons
    ));
    $params = $request->getQueryParams();
    return $view->render($response, 'pages/upload.html.twig', [
        'auth_user' => $user,
        'preview_icons' => array_values($previewIcons),
        'prefill_theme' => $params['theme'] ?? '',
        'prefill_username' => $params['username'] ?? '',
        'prefill_mode' => $params['mode'] ?? '',
        'prefill_description' => $params['description'] ?? '',
        'prefill_version' => $params['version'] ?? '',
    ]);
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

$app->post('/upload', function (Request $request, Response $response) use ($db, $mailer, $jwtSecret) {
    $view = Twig::fromRequest($request);
    $data = $request->getParsedBody();
    $files = $request->getUploadedFiles();
    $authUser = getAuthUser($request, $jwtSecret);

    if (!empty($data['confirm_email'] ?? '')) {
        return $view->render($response, 'pages/upload-sent.html.twig', [
            'email' => 'your inbox',
        ]);
    }

    $themeName = trim($data['theme_name'] ?? '');
    $description = trim($data['theme_description'] ?? '');
    $version = trim($data['theme_version'] ?? '') ?: 'v1.0';
    $isDark = !empty($data['is_dark']);
    $svg = $files['svg_file'] ?? null;

    if ($authUser) {
        $email = $authUser['email'];
        $username = $authUser['username'];
    } else {
        $email = trim($data['email'] ?? '');
        $username = trim($data['username'] ?? '');
    }

    if (!$svg || $svg->getError() !== UPLOAD_ERR_OK || !$email || !$username || !$themeName) {
        return $view->render($response, 'pages/upload.html.twig', [
            'error' => 'All fields are required.',
            'auth_user' => $authUser,
        ]);
    }

    if (!preg_match('/^[A-Za-z0-9_\-]{2,32}$/', $themeName)) {
        return $view->render($response, 'pages/upload.html.twig', [
            'error' => 'Invalid theme name. Use 2–32 characters: letters, numbers, hyphens, underscores.',
            'auth_user' => $authUser,
        ]);
    }

    if (mb_strlen($description) > 200) {
        return $view->render($response, 'pages/upload.html.twig', [
            'error' => 'Description must be 200 characters or fewer.',
            'auth_user' => $authUser,
        ]);
    }

    if (!$authUser) {
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
    }

    $content = (string) $svg->getStream();
    $validator = new SvgValidator();
    $checks = $validator->validate($content);

    $failed = array_filter($checks, fn($c) => !$c['pass']);
    if (!empty($failed)) {
        return $view->render($response, 'pages/upload.html.twig', [
            'error' => 'SVG validation failed. Please fix the issues and try again.',
            'auth_user' => $authUser,
        ]);
    }

    if (!$db->isConnected()) {
        return $view->render($response, 'pages/upload.html.twig', [
            'error' => 'Database unavailable.',
            'auth_user' => $authUser,
        ]);
    }

    if (!$authUser) {
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
    }

    $ownerEmail = $db->getThemeOwnerEmail($themeName);
    if ($ownerEmail !== null && $ownerEmail !== $email) {
        return $view->render($response, 'pages/upload.html.twig', [
            'error' => 'Theme name "' . htmlspecialchars($themeName) . '" is already taken by another user.',
            'auth_user' => $authUser,
        ]);
    }

    if ($ownerEmail !== null && $ownerEmail === $email) {
        $currentVersion = $db->getPublishedVersion($themeName);
        if ($currentVersion !== null) {
            try {
                $currentVer = Version::parse(ltrim($currentVersion, 'v'), false);
                $nextVer = Version::parse(ltrim($version, 'v'), false);
                if ($nextVer->isLessThanOrEqual($currentVer)) {
                    return $view->render($response, 'pages/upload.html.twig', [
                        'error' => 'Version must be higher than the current version (' . $currentVersion . ').',
                        'auth_user' => $authUser,
                    ]);
                }
            } catch (\Throwable $e) {
                return $view->render($response, 'pages/upload.html.twig', [
                    'error' => 'Invalid version format. Use semver (e.g., v1.1, 1.2.0).',
                    'auth_user' => $authUser,
                ]);
            }
        }
    }

    $converter = new SvgConverter();
    $converted = $converter->convert($content);

    if ($authUser) {
        $userId = $authUser['user_id'];
        $themeId = $db->createTheme($themeName, $description, $version, $converted, $isDark);

        if ($ownerEmail !== null && $ownerEmail === $email) {
            $pending = $db->getThemeById($themeId);
            if ($pending !== null) {
                $db->replacePublishedTheme($themeName, $userId, $pending['description'], $pending['version'], $pending['svg_content'], $isDark);
            }
            $db->deleteTheme($themeId);
        } else {
            $db->publishTheme($themeId, $userId);
        }

        return $view->render($response, 'pages/upload-confirmed.html.twig', [
            'theme_name' => $themeName,
            'username' => $username,
        ]);
    }

    $themeId = $db->createTheme($themeName, $description, $version, $converted, $isDark);
    $token = bin2hex(random_bytes(32));
    $db->createMagicToken($token, $email, $username, $themeId);

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $baseUrl = $scheme . '://' . $host;
    $verifyUrl = $baseUrl . '/verify?token=' . urlencode($token);

    if ($mailer->isConnected()) {
        try {
            $mailer->sendUploadLink($email, $token, $themeName, $baseUrl);
        } catch (\Throwable $e) {
            error_log('Mailer error: ' . $e->getMessage());
        }
    }

    return $view->render($response, 'pages/upload-sent.html.twig', [
        'email' => $email,
        'verify_url' => !$mailer->isConnected() ? $verifyUrl : null,
    ]);
});

$app->get('/verify', function (Request $request, Response $response) use ($db, $jwtSecret) {
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
            $currentVersion = $db->getPublishedVersion($themeName);
            if ($currentVersion !== null) {
                try {
                    $currentVer = Version::parse(ltrim($currentVersion, 'v'), false);
                    $nextVer = Version::parse(ltrim($pending['version'], 'v'), false);
                    if ($nextVer->isLessThanOrEqual($currentVer)) {
                        $db->deleteTheme($data['theme_id']);
                        return $view->render($response, 'pages/upload-error.html.twig', [
                            'error' => 'Version must be higher than the current version (' . $currentVersion . '). Please re-upload with a bumped version.',
                        ]);
                    }
                } catch (\Throwable $e) {
                    $db->deleteTheme($data['theme_id']);
                    return $view->render($response, 'pages/upload-error.html.twig', [
                        'error' => 'Invalid version format. Use semver (e.g., v1.1, 1.2.0).',
                    ]);
                }
            }
            $db->replacePublishedTheme($themeName, $userId, $pending['description'], $pending['version'], $pending['svg_content'], (bool) $pending['is_dark']);
        }
        $db->deleteTheme($data['theme_id']);
    } else {
        $db->publishTheme($data['theme_id'], $userId);
    }

    $response = setAuthCookie($response, $jwtSecret, $userId, $data['email'], $data['username']);

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

$app->post('/api/like/{user}/{name}', function (Request $request, Response $response, array $args) use ($db) {
    $themeId = $db->getThemeIdBySlug($args['user'], $args['name']);
    if ($themeId === null) {
        $response->getBody()->write(json_encode(['error' => 'Theme not found']));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }
    $ipHash = getIpHash($request);
    $added = $db->addLike($themeId, $ipHash);
    $count = $db->getLikeCount($themeId);
    $response->getBody()->write(json_encode(['liked' => true, 'added' => $added, 'count' => $count]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/api/theme/{user}/{name}', function (Request $request, Response $response, array $args) use ($db) {
    $svg = $db->getThemeSvg($args['user'], $args['name']);
    if ($svg === null) {
        return $response->withStatus(404);
    }
    $themeId = $db->getThemeIdBySlug($args['user'], $args['name']);
    if ($themeId !== null) {
        $db->incrementDownloadCount($themeId);
    }
    $response->getBody()->write($svg);
    return $response->withHeader('Content-Type', 'image/svg+xml');
});

$app->get('/api/theme/{user}/{name}/version', function (Request $request, Response $response, array $args) use ($db) {
    $theme = $db->getThemeDetail($args['user'], $args['name']);
    if ($theme === null) {
        return $response->withStatus(404);
    }
    $response->getBody()->write($theme['version']);
    return $response->withHeader('Content-Type', 'text/plain');
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

$app->get('/logout', function (Request $request, Response $response) {
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $cookie = 'clarity_auth=; Path=/; HttpOnly; SameSite=Lax; Max-Age=0'
        . ($secure ? '; Secure' : '');
    return $response
        ->withHeader('Set-Cookie', $cookie)
        ->withHeader('Location', '/')
        ->withStatus(302);
});

$app->get('/template', function (Request $request, Response $response) {
    $svg = <<<'SVG'
<?xml version="1.0" encoding="UTF-8"?>
<!-- The placeholder circle MUST stay. Style it any way you like. -->
<svg xmlns="http://www.w3.org/2000/svg"
     viewBox="0 0 128 128" width="128" height="128">

  <circle id="icon-placeholder"
          cx="64" cy="64" r="56"
          fill="#8b8d96" />

  <!-- Your icon goes here. Glyphs, gradients, filters, -->
  <!-- anything that fits inside the placeholder bounds. -->

</svg>
SVG;
    $response->getBody()->write($svg);
    return $response
        ->withHeader('Content-Type', 'image/svg+xml')
        ->withHeader('Content-Disposition', 'attachment; filename="clarity-template.svg"')
        ->withHeader('Cache-Control', 'public, max-age=86400');
});

$app->get('/install', function (Request $request, Response $response) use ($db) {
    $db->incrementCounter('installs');
    return $response
        ->withHeader('Location', 'https://raw.githubusercontent.com/jcubic/Clarity/wasmer/theme/bin/install.sh')
        ->withStatus(302);
});

$app->run();
