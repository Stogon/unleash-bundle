<?php

namespace Stogon\UnleashBundle\Cache;

use Stogon\UnleashBundle\Repository\FeatureRepository;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\HttpKernel\Kernel;

if (Kernel::VERSION_ID >= 50000) {
	class FeatureCacheWarmer implements CacheWarmerInterface
	{
		private FeatureRepository $featureRepository;

		public function __construct(FeatureRepository $featureRepository)
		{
			$this->featureRepository = $featureRepository;
		}

		public function warmUp(string $cacheDir): array
		{
			$this->featureRepository->getFeatures();

			return [];
		}

		public function isOptional(): bool
		{
			return true;
		}
	}
} else {
	class FeatureCacheWarmer implements CacheWarmerInterface
	{
		private FeatureRepository $featureRepository;

		public function __construct(FeatureRepository $featureRepository)
		{
			$this->featureRepository = $featureRepository;
		}

		/**
		 * @param string $cacheDir
		 *
		 * @return array
		 */
		public function warmUp($cacheDir)
		{
			$this->featureRepository->getFeatures();

			return [];
		}

		public function isOptional(): bool
		{
			return true;
		}
	}
}
