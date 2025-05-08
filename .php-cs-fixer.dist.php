<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
            ->in('src')
            ->files()->name('*.php');

$config = new PhpCsFixer\Config();
$config->setRules([
    '@Symfony' => true,
    '@Symfony:risky' => true,
    '@PSR2' => true,
    'array_syntax' => [
        'syntax' => 'short',
    ],
    'declare_strict_types' => true,
    'constant_case' => true,
    'no_superfluous_phpdoc_tags' => ['remove_inheritdoc' => false],
    'operator_linebreak' => ['position' => 'beginning'],
    'combine_consecutive_unsets' => true,
    'native_function_invocation' => [
        'include' => [
            '@compiler_optimized',
        ],
    ],
    'no_extra_blank_lines' => ['tokens' => [
        'break',
        'continue',
        'extra',
        'return',
        'throw',
        'use',
        'parenthesis_brace_block',
        'square_brace_block',
        'curly_brace_block',
    ]],
    'yoda_style' => [
        'always_move_variable' => false,
        'equal' => false,
        'identical' => false,
        'less_and_greater' => false,
    ],
    'ordered_class_elements' => true,
    'ordered_imports' => true,
])
    ->setRiskyAllowed(true)
    ->setFinder(
        $finder
    )
;

return $config;
