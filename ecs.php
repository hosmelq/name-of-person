<?php

declare(strict_types=1);

use HosmelQ\PhpCodingStandard\CodingStandard;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withCache('./.cache/ecs')
    ->withPaths([__DIR__.'/src', __DIR__.'/tests'])
    ->withRootFiles()
    ->withSets([CodingStandard::ECS_PHP]);
