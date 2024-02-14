<?php

namespace Stogon\UnleashBundle;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Stogon\UnleashBundle\Event\UnleashContextEvent;
use Stogon\UnleashBundle\Repository\FeatureRepository;
use Stogon\UnleashBundle\Strategy\StrategyInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class Unleash implements UnleashInterface
{
	protected RequestStack $requestStack;
	protected TokenStorageInterface $tokenStorage;
	protected EventDispatcherInterface $eventDispatcher;
	protected FeatureRepository $featureRepository;
	/** @var iterable<StrategyInterface> */
	protected iterable $strategiesMapping;
	protected LoggerInterface $logger;

	public function __construct(
		RequestStack $requestStack,
		TokenStorageInterface $tokenStorage,
		EventDispatcherInterface $eventDispatcher,
		FeatureRepository $featureRepository,
		iterable $strategiesMapping,
		?LoggerInterface $logger = null
	) {
		$this->requestStack = $requestStack;
		$this->tokenStorage = $tokenStorage;
		$this->eventDispatcher = $eventDispatcher;
		$this->featureRepository = $featureRepository;
		$this->strategiesMapping = $strategiesMapping;
		$this->logger = $logger ?: new NullLogger();
	}

	public function getFeatures(): array
	{
		return $this->featureRepository->getFeatures();
	}

	public function getFeature(string $name): ?Feature
	{
		return $this->featureRepository->getFeature($name);
	}

	public function isFeatureEnabled(string $name, bool $defaultValue = false): bool
	{
		$feature = $this->featureRepository->getFeature($name);

		if ($feature === null || $feature->isDisabled()) {
			$this->logger->debug('Feature was not found or is disabled', [
				'name' => $name,
				'feature' => $feature,
				'default_value' => $defaultValue,
			]);

			return false;
		}

		$this->logger->debug('Found feature matching the given name', [
			'feature' => $feature,
			'name' => $name,
		]);

		$strategies = iterator_to_array($this->strategiesMapping);
		$token = $this->tokenStorage->getToken();
		$user = null;

		if ($token !== null) {
			$user = $token->getUser();
			$this->logger->debug('Using authenticated user from token', [
				'name' => $name,
				'feature' => $feature,
				'default_value' => $defaultValue,
				'token' => $token,
				'user' => $user,
			]);
		}

		$event = new UnleashContextEvent([
			'request' => $this->requestStack->getMainRequest(),
			'user' => $user,
		]);

		$this->eventDispatcher->dispatch($event);

		$context = $event->getPayload();

		foreach ($feature->getStrategies() as $strategyData) {
			$strategyName = $strategyData['name'];

			if (!array_key_exists($strategyName, $strategies)) {
				return false;
			}

			$strategy = $strategies[$strategyName];

			if (!$strategy instanceof StrategyInterface) {
				throw new \Exception(sprintf('%s does not implement %s interface.', $strategyName, StrategyInterface::class));
			}

			if ($strategy->isEnabled($strategyData['parameters'] ?? [], $context)) {
				$this->logger->debug('Feature flag is enabled for given context', [
					'name' => $name,
					'feature' => $feature,
					'strategy' => $strategy,
					'strategy_data' => $strategyData,
					'context' => $context,
				]);

				return true;
			}
		}

		$this->logger->debug('No strategy could confirm that the feature flag is enabled for given context. Returning default value passed as parameter.', [
			'name' => $name,
			'feature' => $feature,
			'context' => $context,
			'default_value' => $defaultValue,
		]);

		return $defaultValue;
	}

	public function isFeatureDisabled(string $name, bool $defaultValue = true): bool
	{
		return !$this->isFeatureEnabled($name, $defaultValue);
	}
}
