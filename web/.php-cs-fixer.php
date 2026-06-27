<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->append([__DIR__ . '/index.php']);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS' => true,
        'braces_position' => [
            'classes_opening_brace' => 'same_line',
            'functions_opening_brace' => 'same_line',
            'anonymous_classes_opening_brace' => 'same_line',
            'anonymous_functions_opening_brace' => 'same_line',
            'control_structures_opening_brace' => 'same_line',
        ],
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'single_quote' => true,
        'trailing_comma_in_multiline' => true,
    ])
    ->setFinder($finder);
