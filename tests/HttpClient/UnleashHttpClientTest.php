<?php

namespace Stogon\UnleashBundle\Tests\HttpClient;

use PHPUnit\Framework\TestCase;
use Stogon\UnleashBundle\HttpClient\UnleashHttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TimeoutExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[\PHPUnit\Framework\Attributes\CoversClass(UnleashHttpClient::class)]
class UnleashHttpClientTest extends TestCase
{
	public function testFetchFeaturesRequestThrows(): void
	{
		$httpClient = $this->createMock(HttpClientInterface::class);
		$httpClient
			->method('request')
			->willThrowException(
				$this->createMock(TimeoutExceptionInterface::class)
			);

		$client = new UnleashHttpClient(
			$httpClient,
			'',
			'',
			''
		);

		$result = $client->fetchFeatures();

		self::assertEquals([], $result);
	}

	public function testFetchFeaturesToArrayThrows(): void
	{
		$response = $this->createMock(ResponseInterface::class);
		$response
			->method('toArray')->willThrowException(
				$this->createMock(ClientExceptionInterface::class)
			);

		$httpClient = $this->createMock(HttpClientInterface::class);
		$httpClient
			->method('request')
			->with('GET', 'client/features')
			->willReturn($response);

		$client = new UnleashHttpClient(
			$httpClient,
			'',
			'',
			''
		);

		$result = $client->fetchFeatures();

		self::assertEquals([], $result);
	}

	public function testFetchFeaturesArrayFeatureKeyExists(): void
	{
		$response = $this->createMock(ResponseInterface::class);
		$response
			->method('toArray')
			->willReturn([
				'features' => ['foo', 'bar'],
			]);

		$httpClient = $this->createMock(HttpClientInterface::class);
		$httpClient
			->method('request')
			->with('GET', 'client/features')
			->willReturn($response);

		$client = new UnleashHttpClient(
			$httpClient,
			'',
			'',
			''
		);

		$result = $client->fetchFeatures();

		self::assertEquals(['foo', 'bar'], $result);
	}

	public function testFetchFeaturesReturnsDefault(): void
	{
		$response = $this->createMock(ResponseInterface::class);
		$response
			->method('toArray')
			->willReturn([]);

		$httpClient = $this->createMock(HttpClientInterface::class);
		$httpClient
			->method('request')
			->with('GET', 'client/features')
			->willReturn($response);

		$client = new UnleashHttpClient(
			$httpClient,
			'',
			'',
			''
		);

		$result = $client->fetchFeatures();

		self::assertEquals([], $result);
	}
}
