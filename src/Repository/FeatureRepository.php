<?php

namespace Stogon\UnleashBundle\Repository;

use Stogon\UnleashBundle\Feature;
use Stogon\UnleashBundle\HttpClient\UnleashHttpClient;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class FeatureRepository
{
	protected UnleashHttpClient $client;
	protected CacheInterface $cache;
	protected int $ttl;

	public function __construct(UnleashHttpClient $httpClient, CacheInterface $cache, int $ttl)
	{
		$this->client = $httpClient;
		$this->cache = $cache;
		$this->ttl = $ttl;
	}

	/**
	 * @return Feature[]
	 */
	public function getFeatures(): array
	{
		return $this->cache->get('unleash.strategies', function (ItemInterface $item): array {
			$features = $this->client->fetchFeatures();

			$item->expiresAfter($this->ttl);

			return array_map(function (array $feature): Feature {
				return new Feature(
					$feature['name'],
					$feature['description'],
					$feature['enabled'],
					$feature['strategies']
				);
			}, $features);
		});
	}

	public function getFeature(string $name): ?Feature
	{
		$features = $this->getFeatures();

		$filtered = array_filter($features, fn (Feature $f): bool => $f->getName() === $name);

		if (!empty($filtered)) {
			return $filtered[array_key_first($filtered)];
		}

		return null;
	}
}
