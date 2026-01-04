<?php

namespace Stogon\UnleashBundle\Tests\Cache;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Stogon\UnleashBundle\Cache\FeatureCacheWarmer;
use Stogon\UnleashBundle\Repository\FeatureRepository;

#[CoversClass(FeatureCacheWarmer::class)]
class FeatureCacheWarmerTest extends TestCase
{
	/**
	 * @param array{featureRepository?: FeatureRepository} $args
	 */
	protected function buildInstance(array $args = []): FeatureCacheWarmer
	{
		return new FeatureCacheWarmer(...[
			'featureRepository' => $this->createMock(FeatureRepository::class),
			...$args,
		]);
	}

	public function testWarmUp(): void
	{
		$featureRepository = $this->createMock(FeatureRepository::class);
		$featureRepository->expects($this->once())
			->method('getFeatures');

		$warmer = $this->buildInstance([
			'featureRepository' => $featureRepository,
		]);

		$files = $warmer->warmUp(__DIR__, __DIR__);

		// There should be no file to preload
		$this->assertCount(0, $files);
	}

	public function testIsOptional(): void
	{
		$warmer = $this->buildInstance();

		$this->assertTrue($warmer->isOptional());
	}
}
