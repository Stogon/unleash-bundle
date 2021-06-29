<?php

namespace Stogon\UnleashBundle\Strategy;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\User\UserInterface;

class UserWithIdStrategy implements StrategyInterface
{
	public function isEnabled(array $parameters = [], array $context = [], ...$args): bool
	{
		$userIds = $parameters['userIds'] ?? [];

		$ids = array_map('trim', explode(',', $userIds));

		if ($context['user']) {
			/** @var string|\Stringable|UserInterface */
			$currentUser = $context['user'];

			if (is_string($currentUser)) {
				return in_array($currentUser, $ids, true);
			}

			if (method_exists($currentUser, 'getId') && in_array($currentUser->getId(), $ids, true)) {
				return true;
			}

			if ($currentUser instanceof UserInterface) {
				if (Kernel::VERSION_ID >= 50300 && method_exists($currentUser, 'getUserIdentifier')) {
					return in_array($currentUser->getUserIdentifier(), $ids, true);
				}

				return in_array($currentUser->getUsername(), $ids, true);
			}

			return in_array((string) $currentUser, $ids, true);
		}

		/** @var Request */
		$request = $context['request'];

		return in_array($request->getUser(), $ids, true);
	}
}
