<?php

namespace Stogon\UnleashBundle\Cache;

use Stogon\UnleashBundle\Repository\FeatureRepository;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class FeatureCacheWarmer implements CacheWarmerInterface
{
	public function __construct(private readonly FeatureRepository $featureRepository)
	{
	}

	public function warmUp(string $cacheDir, ?string $buildDir = null): array
	{
		$this->featureRepository->getFeatures();

		return [];
	}

	public function isOptional(): bool
	{
		return true;
	}
}
