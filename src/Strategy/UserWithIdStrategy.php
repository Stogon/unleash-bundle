<?php

namespace Stogon\UnleashBundle\Strategy;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class UserWithIdStrategy implements StrategyInterface
{
	public function isEnabled(array $parameters = [], array $context = [], ...$args): bool
	{
		$userIds = $parameters['userIds'] ?? [];

		$ids = array_map('trim', explode(',', $userIds));

		if ($context['user']) {
			/** @var UserInterface */
			$currentUser = $context['user'];

			if (method_exists($currentUser, 'getId') && in_array($currentUser->getId(), $ids, false)) {
				return true;
			}

			if ($currentUser instanceof UserInterface) {
				return in_array($currentUser->getUserIdentifier(), $ids, false);
			}

			return in_array((string) $currentUser, $ids, false);
		}

		/** @var Request */
		$request = $context['request'];

		return in_array($request->getUser(), $ids, false);
	}
}
