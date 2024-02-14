<?php

namespace Stogon\UnleashBundle\Cache;

use Stogon\UnleashBundle\Repository\FeatureRepository;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class FeatureCacheWarmer implements CacheWarmerInterface
{
	private FeatureRepository $featureRepository;

	public function __construct(FeatureRepository $featureRepository)
	{
		$this->featureRepository = $featureRepository;
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
