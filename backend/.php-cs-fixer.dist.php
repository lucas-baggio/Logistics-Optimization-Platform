<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/app')
    ->in(__DIR__ . '/database')
    ->in(__DIR__ . '/routes')
    ->in(__DIR__ . '/tests')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => ['default' => 'align_single_space'],
        'concat_space' => ['spacing' => 'one'],
        'yoda_style' => false,
        'no_unused_imports' => true,
        'ordered_imports' => true,
        'single_quote' => true,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
;
