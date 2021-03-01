<?php

namespace Stogon\UnleashBundle;

interface UnleashInterface
{
	/**
	 * Return all features available for the current instance.
	 *
	 * @return FeatureInterface[]
	 */
	public function getFeatures(): array;

	/**
	 * Get a feature by his name.
	 */
	public function getFeature(string $name): ?FeatureInterface;

	/**
	 * Check if a feature is enabled.
	 */
	public function isFeatureEnabled(string $name, bool $defaultValue = false): bool;
}
