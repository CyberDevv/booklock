<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\ArrowFunction\AddArrowFunctionReturnTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictTypedCallRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnUnionTypeRector;
use Rector\TypeDeclaration\Rector\Closure\AddClosureVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;
use RectorLaravel\Set\LaravelSetProvider;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/bootstrap',
        __DIR__.'/config',
        __DIR__.'/database',
        __DIR__.'/public',
        __DIR__.'/resources',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ])
    ->withSkip([
        __DIR__.'/bootstrap/cache',
        __DIR__.'/storage',
        __DIR__.'/vendor',

        // Skip aggressive rules
        AddClosureVoidReturnTypeWhereNoReturnRector::class,
        ReturnTypeFromStrictTypedCallRector::class,
        ReturnUnionTypeRector::class,
        AddArrowFunctionReturnTypeRector::class,
        DeclareStrictTypesRector::class => [
            __DIR__.'/resources/views',
        ],
    ])
    ->withComposerBased(laravel: true)
    ->withSetProviders(LaravelSetProvider::class)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        earlyReturn: true,
    )
    ->withImportNames(removeUnusedImports: true)
    ->withPhpSets(php84: true)
    ->withRules([
        DeclareStrictTypesRector::class,
    ]);
