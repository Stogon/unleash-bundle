<?php

namespace Stogon\UnleashBundle\Strategy;

class UserWithIdStrategy implements StrategyInterface
{
	public function isEnabled(array $parameters = [], array $context = [], ...$args): bool
	{
		$userIds = $parameters['userIds'] ?? [];

		$ids = explode(',', $userIds);

		if (is_string($context['user'])) {
			return in_array($context['user'], $ids, true);
		}

		return in_array($context['user']->getId(), $ids, true);
	}
}
