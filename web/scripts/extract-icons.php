<?php

$base = dirname(__DIR__);
$files_list = $base . '/files.txt';
$theme_dir = $base . '/../theme';
$output = $base . '/icons.json';

$lines = file($files_list, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$icons = [];

foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#') continue;

    $path = $theme_dir . '/' . $line;
    if (!file_exists($path)) {
        fprintf(STDERR, "warning: %s not found, skipping\n", $line);
        continue;
    }

    $svg = file_get_contents($path);
    if (preg_match('/\bd="([^"]+)"/', $svg, $m)) {
        $name = pathinfo($line, PATHINFO_FILENAME);
        $category = basename(dirname($line));
        $icons[] = [
            'name' => $name,
            'category' => $category,
            'd' => $m[1],
        ];
    } else {
        fprintf(STDERR, "warning: no path d= found in %s\n", $line);
    }
}

file_put_contents($output, json_encode($icons, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
fprintf(STDERR, "extracted %d icons to %s\n", count($icons), $output);
