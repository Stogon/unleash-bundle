<?php

namespace Stogon\UnleashBundle\Strategy;

class DefaultStrategy implements StrategyInterface
{
	public function isEnabled(array $parameters = [], array $context = [], ...$args): bool
	{
		return true;
	}
}
