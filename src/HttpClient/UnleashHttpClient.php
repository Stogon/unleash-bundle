<?php

namespace Stogon\UnleashBundle\HttpClient;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UnleashHttpClient implements LoggerAwareInterface
{
	use LoggerAwareTrait;

	public function __construct(
		private readonly HttpClientInterface $httpClient,
		protected readonly string $apiUrl,
		protected readonly string $instanceId,
		protected readonly string $environment
	) {
		$this->logger = new NullLogger();
	}

	public function fetchFeatures(): array
	{
		try {
			$response = $this->httpClient->request('GET', 'client/features');
			$features = $response->toArray();
		} catch (\Throwable $th) {
			$this->logger->critical('Could not fetch features flags', [
				'exception' => $th,
			]);

			return [];
		}

		if (array_key_exists('features', $features)) {
			$this->logger->debug('Fetched feature flags from remote', [
				'feature_flags' => $features['features'],
			]);

			return $features['features'];
		}

		return [];
	}
}
