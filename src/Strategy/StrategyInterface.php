<?php

namespace Stogon\UnleashBundle\Strategy;

interface StrategyInterface
{
	public function isEnabled(array $parameters = [], array $context = [], ...$args): bool;
}
