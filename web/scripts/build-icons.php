<?php

$base = dirname(__DIR__);
$icons = json_decode(file_get_contents($base . '/icons.json'), true);
$template_dir = $base . '/../theme/src';
$output_dir = $base . '/icons';

$templates = glob($template_dir . '/template_*.svg');
if (!$templates) {
    fprintf(STDERR, "error: no templates found in %s\n", $template_dir);
    exit(1);
}

$count = 0;

foreach ($templates as $tpl_path) {
    $variant = preg_replace('/^template_/', '', pathinfo($tpl_path, PATHINFO_FILENAME));
    $raw = file_get_contents($tpl_path);

    $optimized = preg_replace('/<\?xml[^?]*\?>\s*/', '', $raw);
    $optimized = preg_replace('/\s*xmlns:dc="[^"]*"/', '', $optimized);
    $optimized = preg_replace('/\s*xmlns:cc="[^"]*"/', '', $optimized);
    $optimized = preg_replace('/\s*xmlns:rdf="[^"]*"/', '', $optimized);
    $optimized = preg_replace('/\s*xmlns:svg="[^"]*"/', '', $optimized);
    $optimized = preg_replace('/\s*xmlns:xlink="[^"]*"/', '', $optimized);
    $optimized = str_replace('xlink:href', 'href', $optimized);
    $optimized = preg_replace('/\s*version="[^"]*"/', '', $optimized);
    $optimized = preg_replace('/\s*<desc>[^<]*<\/desc>/', '', $optimized);
    $optimized = preg_replace('/\s*<metadata>.*?<\/metadata>/s', '', $optimized);
    $optimized = preg_replace('/\s*id="(start|stop)"/', '', $optimized);
    $optimized = preg_replace('/;stop-opacity:1/', '', $optimized);
    $optimized = preg_replace('/;fill-opacity:1;stroke:none/', '', $optimized);
    $optimized = preg_replace('/\n\s*\n/', "\n", $optimized);

    $dir = $output_dir . '/' . $variant;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    foreach ($icons as $icon) {
        $title = ucfirst(str_replace('-', ' ', $icon['name']));
        $svg = str_replace(['{{TITLE}}', '{{PATH}}'], [$title, $icon['d']], $optimized);
        file_put_contents($dir . '/' . $icon['name'] . '.svg', $svg);
        $count++;
    }
}

$variant_count = count($templates);
$icon_count = count($icons);
fprintf(STDERR, "built %d SVG files (%d variants × %d icons) in %s\n", $count, $variant_count, $icon_count, $output_dir);
