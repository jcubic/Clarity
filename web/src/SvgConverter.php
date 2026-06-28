<?php

namespace Clarity;

class SvgConverter {
    public function convert(string $svg): string {
        if (str_contains($svg, '{{PATH}}')) {
            return $svg;
        }

        $result = preg_replace('/<title>[^<]*<\/title>/', '<title>{{TITLE}}</title>', $svg, 1, $count);
        if ($result !== null) {
            $svg = $result;
        }

        if (!str_contains($svg, '{{TITLE}}')) {
            $svg = preg_replace('/<svg([^>]*)>/', '<svg$1>' . "\n    <title>{{TITLE}}</title>", $svg, 1) ?? $svg;
        }

        $convertCircle = function (array $m): string {
            $attrs = $m[1];
            $attrs = preg_replace('/\s*id="icon-placeholder"/', '', $attrs) ?? $attrs;
            $attrs = preg_replace('/\s*cx="[^"]*"/', '', $attrs) ?? $attrs;
            $attrs = preg_replace('/\s*cy="[^"]*"/', '', $attrs) ?? $attrs;
            $attrs = preg_replace('/\s*\br="[^"]*"/', '', $attrs) ?? $attrs;
            return '<path d="{{PATH}}"' . $attrs . '/>';
        };

        $svg = preg_replace_callback(
            '/<circle([^>]*id="icon-placeholder"[^>]*)\/>/',
            $convertCircle,
            $svg
        ) ?? $svg;

        $svg = preg_replace_callback(
            '/<circle([^>]*id="icon-placeholder"[^>]*)>[^<]*<\/circle>/',
            $convertCircle,
            $svg
        ) ?? $svg;

        return $svg;
    }
}
