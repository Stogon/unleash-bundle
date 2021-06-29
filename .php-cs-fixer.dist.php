<?php
/*
 * This document has been generated with
 * https://mlocati.github.io/php-cs-fixer-configurator/?version=2.15#configurator
 * you can change this configuration by importing this file.
 */

return (new PhpCsFixer\Config())
    ->setIndent("\t")
    ->setRules([
        '@Symfony' => true,
        '@PHP70Migration' => true,
        '@PHP71Migration' => true,
        '@PHP73Migration' => true,
        '@PHP74Migration' => true,
        'array_syntax' => ['syntax' => 'short'],
        'yoda_style' => false,
        'ordered_imports' => true,
        'phpdoc_to_comment' => false,
    ])
    ->setFinder(PhpCsFixer\Finder::create()
        ->exclude(['vendor'])
        ->in(__DIR__)
    )
;
