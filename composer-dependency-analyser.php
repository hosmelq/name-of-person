<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

return (new Configuration())
    ->ignoreErrors([ErrorType::SHADOW_DEPENDENCY])
    ->ignoreErrorsOnPackages([], [ErrorType::UNUSED_DEPENDENCY]);
