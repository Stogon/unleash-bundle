<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
	->withPaths([
		__DIR__.'/config',
		__DIR__.'/src',
		__DIR__.'/tests',
	])
	// uncomment to reach your current PHP version
	->withPhpSets(php81: true)
	->withAttributesSets(symfony: true, phpunit: true)
	->withTypeCoverageLevel(0);
