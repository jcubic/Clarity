<?php

namespace Clarity;

class SvgValidator {
    private const MAX_SIZE = 256 * 1024;

    /** @return array<string, array{pass: bool, hint: string}> */
    public function validate(string $svg): array {
        $filesize = $this->checkFileSize($svg);

        $doc = new \DOMDocument();
        $prev = libxml_use_internal_errors(true);
        $loaded = $doc->loadXML($svg);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        if (!$loaded) {
            $fail = $this->allFail('File is not valid XML.');
            $fail['filesize'] = $filesize;
            return $fail;
        }

        $root = $doc->documentElement;
        if (!$root || strtolower($root->localName) !== 'svg') {
            $fail = $this->allFail('Root element must be <svg>.');
            $fail['filesize'] = $filesize;
            return $fail;
        }

        return [
            'filesize' => $filesize,
            'canvas' => $this->checkCanvas($root),
            'placeholder' => $this->checkPlaceholder($doc),
            'vector' => $this->checkVector($doc),
            'security' => $this->checkSecurity($doc, $svg),
            'ids' => $this->checkDuplicateIds($doc),
        ];
    }

    /** @return array{pass: bool, hint: string} */
    private function checkFileSize(string $svg): array {
        $size = strlen($svg);
        if ($size <= self::MAX_SIZE) {
            return ['pass' => true, 'hint' => ''];
        }
        $kb = number_format($size / 1024, 1);
        return ['pass' => false, 'hint' => "File is {$kb} KB. Simplify paths or remove unnecessary metadata."];
    }

    /** @return array{pass: bool, hint: string} */
    private function checkCanvas(\DOMElement $root): array {
        $viewBox = $root->getAttribute('viewBox');
        $w = $root->getAttribute('width');
        $h = $root->getAttribute('height');

        $vbOk = false;
        if ($viewBox) {
            $parts = preg_split('/[\s,]+/', trim($viewBox));
            $vbOk = $parts !== false
                && count($parts) === 4
                && (float) $parts[0] === 0.0
                && (float) $parts[1] === 0.0
                && (float) $parts[2] === 128.0
                && (float) $parts[3] === 128.0;
        }

        $sizeOk = ((float) $w === 128.0 && (float) $h === 128.0);

        if ($vbOk && $sizeOk) {
            return ['pass' => true, 'hint' => ''];
        }

        if (!$vbOk && !$sizeOk) {
            return ['pass' => false, 'hint' => 'Set viewBox="0 0 128 128" and width/height to 128.'];
        }
        if (!$vbOk) {
            return ['pass' => false, 'hint' => 'Set viewBox="0 0 128 128".'];
        }
        return ['pass' => false, 'hint' => 'Set width="128" height="128".'];
    }

    /** @return array{pass: bool, hint: string} */
    private function checkPlaceholder(\DOMDocument $doc): array {
        $circles = $doc->getElementsByTagName('circle');
        for ($i = 0; $i < $circles->length; $i++) {
            $c = $circles->item($i);
            if ($c instanceof \DOMElement && $c->getAttribute('id') === 'icon-placeholder') {
                return ['pass' => true, 'hint' => ''];
            }
        }
        return ['pass' => false, 'hint' => 'Keep the <circle id="icon-placeholder"> element. It marks where the icon path is injected.'];
    }

    /** @return array{pass: bool, hint: string} */
    private function checkVector(\DOMDocument $doc): array {
        $images = $doc->getElementsByTagName('image');
        for ($i = 0; $i < $images->length; $i++) {
            $img = $images->item($i);
            if (!$img instanceof \DOMElement) {
                continue;
            }
            $href = $img->getAttribute('href') ?: $img->getAttributeNS('http://www.w3.org/1999/xlink', 'href');
            if ($href && preg_match('/^data:image\/(png|jpe?g|gif|bmp|webp)/i', $href)) {
                return ['pass' => false, 'hint' => 'Remove embedded raster <image> elements. Use vector paths only.'];
            }
            if ($href && !str_starts_with($href, 'data:image/svg')) {
                return ['pass' => false, 'hint' => 'Remove <image> elements referencing raster files.'];
            }
        }
        return ['pass' => true, 'hint' => ''];
    }

    /** @return array{pass: bool, hint: string} */
    private function checkSecurity(\DOMDocument $doc, string $svg): array {
        $dangerous = ['script', 'foreignObject'];
        foreach ($dangerous as $tag) {
            if ($doc->getElementsByTagName($tag)->length > 0) {
                return ['pass' => false, 'hint' => "Remove all <{$tag}> elements."];
            }
        }

        if (preg_match('/\bon\w+\s*=/i', $svg)) {
            return ['pass' => false, 'hint' => 'Remove inline event handlers (onclick, onload, etc.).'];
        }

        if (preg_match('/xlink:href\s*=\s*["\']https?:/i', $svg)
            || preg_match('/href\s*=\s*["\']https?:/i', $svg)) {
            return ['pass' => false, 'hint' => 'Remove external URL references (href="http...").'];
        }

        return ['pass' => true, 'hint' => ''];
    }

    /** @return array{pass: bool, hint: string} */
    private function checkDuplicateIds(\DOMDocument $doc): array {
        $ids = [];
        $xpath = new \DOMXPath($doc);
        $nodes = $xpath->query('//*[@id]');
        if ($nodes === false) {
            return ['pass' => true, 'hint' => ''];
        }
        foreach ($nodes as $node) {
            if (!$node instanceof \DOMElement) {
                continue;
            }
            $id = $node->getAttribute('id');
            if (isset($ids[$id])) {
                return ['pass' => false, 'hint' => "Duplicate id=\"{$id}\" found. Each id must be unique."];
            }
            $ids[$id] = true;
        }
        return ['pass' => true, 'hint' => ''];
    }

    /** @return array<string, array{pass: bool, hint: string}> */
    private function allFail(string $hint): array {
        $fail = ['pass' => false, 'hint' => $hint];
        return [
            'canvas' => $fail,
            'placeholder' => $fail,
            'vector' => $fail,
            'security' => $fail,
            'ids' => $fail,
        ];
    }
}
