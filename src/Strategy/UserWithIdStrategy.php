<?php

namespace Stogon\UnleashBundle\Strategy;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class UserWithIdStrategy implements StrategyInterface
{
	public function isEnabled(array $parameters = [], array $context = [], mixed ...$args): bool
	{
		$userIds = $parameters['userIds'] ?? [];

		$ids = array_map('trim', explode(',', (string) $userIds));

		if ($context['user']) {
			$currentUser = $context['user'];

			if ($currentUser instanceof UserInterface) {
				return in_array($currentUser->getUserIdentifier(), $ids, false);
			}

			if (is_object($currentUser) && method_exists($currentUser, 'getId') && in_array($currentUser->getId(), $ids, false)) {
				return true;
			}
		}

		/** @var Request|null */
		$request = $context['request'] ?? null;

		return in_array($request?->getUser(), $ids, false);
	}
}
