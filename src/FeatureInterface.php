<?php

namespace Stogon\UnleashBundle;

use Stogon\UnleashBundle\Strategy\StrategyInterface;

interface FeatureInterface
{
	/**
	 * Name of the feature.
	 */
	public function getName(): string;

	/**
	 * Description of the feature.
	 */
	public function getDescription(): string;

	/**
	 * If the feature flag is enabled.
	 */
	public function isEnabled(): bool;

	/**
	 * If the feature flag is disabled.
	 */
	public function isDisabled(): bool;

	/**
	 * @return StrategyInterface[]
	 */
	public function getStrategies(): array;
}
