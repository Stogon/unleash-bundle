<?php

namespace Stogon\UnleashBundle\Twig;

use Stogon\UnleashBundle\UnleashInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UnleashExtension extends AbstractExtension
{
	public function __construct(protected readonly UnleashInterface $unleash)
	{
	}

	public function getFunctions(): array
	{
		return [
			new TwigFunction('is_feature_enabled', $this->isFeatureEnabled(...)),
			new TwigFunction('is_feature_disabled', $this->isFeatureDisabled(...)),
		];
	}

	public function isFeatureEnabled(string $name, bool $defaultValue = false): bool
	{
		return $this->unleash->isFeatureEnabled($name, $defaultValue);
	}

	public function isFeatureDisabled(string $name, bool $defaultValue = true): bool
	{
		return $this->unleash->isFeatureDisabled($name, $defaultValue);
	}
}
