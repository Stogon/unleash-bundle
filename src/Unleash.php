<?php

namespace Stogon\UnleashBundle;

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

	public function __construct(
		RequestStack $requestStack,
		TokenStorageInterface $tokenStorage,
		EventDispatcherInterface $eventDispatcher,
		FeatureRepository $featureRepository,
		iterable $strategiesMapping
	) {
		$this->requestStack = $requestStack;
		$this->tokenStorage = $tokenStorage;
		$this->eventDispatcher = $eventDispatcher;
		$this->featureRepository = $featureRepository;
		$this->strategiesMapping = $strategiesMapping;
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

		if ($feature === null) {
			return false;
		}

		$strategies = iterator_to_array($this->strategiesMapping);
		$token = $this->tokenStorage->getToken();
		$user = null;

		if ($token !== null && $token->isAuthenticated()) {
			$user = $token->getUser();
		}

		$event = new UnleashContextEvent([
			'request' => $this->requestStack->getMasterRequest(),
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
				return true;
			}
		}

		return $defaultValue;
	}

	public function isFeatureDisabled(string $name, bool $defaultValue = true): bool
	{
		return !$this->isFeatureEnabled($name, $defaultValue);
	}
}
