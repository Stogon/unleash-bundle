<?php

namespace Stogon\UnleashBundle\HttpClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class UnleashHttpClient
{
	private HttpClientInterface $httpClient;
	protected string $apiUrl;
	protected string $instanceId;
	protected string $environment;

	public function __construct(HttpClientInterface $unleashClient, string $apiUrl, string $instanceId, string $environment)
	{
		$this->httpClient = $unleashClient;
		$this->apiUrl = $apiUrl;
		$this->instanceId = $instanceId;
		$this->environment = $environment;
	}

	public function fetchFeatures(): array
	{
		$response = $this->httpClient->request('GET', 'client/features');

		$features = $response->toArray();

		if (array_key_exists('features', $features)) {
			return $features['features'];
		}

		return [];
	}
}
