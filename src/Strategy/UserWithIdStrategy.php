<?php

namespace Stogon\UnleashBundle\Strategy;

use Symfony\Component\Security\Core\User\UserInterface;

class UserWithIdStrategy implements StrategyInterface
{
	public function isEnabled(array $parameters = [], array $context = [], ...$args): bool
	{
		$userIds = $parameters['userIds'] ?? [];

		$ids = explode(',', $userIds);

		$currentUser = $context['user'];

		if (is_string($currentUser)) {
			return in_array($currentUser, $ids, true);
		}

		if (method_exists($currentUser, 'getId')) {
			return in_array($currentUser->getId(), $ids, true);
		}

		if ($currentUser instanceof UserInterface) {
			return in_array($currentUser->getUsername(), $ids, true);
		}

		return in_array((string) $currentUser, $ids, true);
	}
}
