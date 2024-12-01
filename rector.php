<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Privatization\Rector\Property\PrivatizeFinalClassPropertyRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/bootstrap',
        __DIR__.'/config',
        __DIR__.'/public',
        __DIR__.'/resources',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ])
    // uncomment to reach your current PHP version
    // ->withPhpSets()
    ->withPreparedSets(
        codeQuality: true,
        privatization: true,
        instanceOf: true,
        strictBooleans: true,
    )
    ->withSkip([
        PrivatizeFinalClassPropertyRector::class => [__DIR__.'/app/Http/Controllers/Auth'],
    ])
    ->withTypeCoverageLevel(45);
