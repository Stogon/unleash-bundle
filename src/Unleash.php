<?php

namespace Stogon\UnleashBundle;

use Stogon\UnleashBundle\Repository\FeatureRepository;
use Stogon\UnleashBundle\Strategy\StrategyInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class Unleash implements UnleashInterface
{
	protected RequestStack $requestStack;
	protected TokenStorageInterface $tokenStorage;
	protected FeatureRepository $featureRepository;
	protected array $strategiesMapping;

	public function __construct(
		RequestStack $requestStack,
		TokenStorageInterface $tokenStorage,
		FeatureRepository $featureRepository,
		array $strategiesMapping
	) {
		$this->requestStack = $requestStack;
		$this->tokenStorage = $tokenStorage;
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

		$token = $this->tokenStorage->getToken();
		$user = null;

		if ($token !== null && $token->isAuthenticated()) {
			$user = $token->getUser();
		}

		$context = [
			'request' => $this->requestStack->getMasterRequest(),
			'user' => $user,
		];

		foreach ($feature->getStrategies() as $strategyData) {
			$className = $strategyData['name'];

			if (!array_key_exists($className, $this->strategiesMapping)) {
				return false;
			}

			if (is_callable($this->strategiesMapping[$className])) {
				$strategy = $this->strategiesMapping[$className]();
			} else {
				$strategy = new $this->strategiesMapping[$className]();
			}

			if (!$strategy instanceof StrategyInterface) {
				throw new \Exception(sprintf('%s does not implement %s interface.', $className, StrategyInterface::class));
			}

			if (!$strategy->isEnabled($strategyData['parameters'] ?? [], $context)) {
				return false;
			}
		}

		return $defaultValue;
	}
}
