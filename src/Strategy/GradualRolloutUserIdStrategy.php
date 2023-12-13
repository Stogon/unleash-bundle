<?php

namespace Stogon\UnleashBundle\Strategy;

use Stogon\UnleashBundle\Helper\ValueNormalizer;
use Symfony\Component\Security\Core\User\UserInterface;

class GradualRolloutUserIdStrategy implements StrategyInterface
{
	public function isEnabled(array $parameters = [], array $context = [], ...$args): bool
	{
		$percentage = intval($parameters['percentage'] ?? 0);
		$groupId = trim($parameters['groupId'] ?? '');
		$userId = trim($this->getUserId($context) ?? '');

		if (!$userId) {
			return false;
		}

		$userIdValue = ValueNormalizer::build($userId, $groupId);

		return $percentage > 0 && $userIdValue <= $percentage;
	}

	protected function getUserId(array $context): ?string
	{
		if (array_key_exists('user', $context) && $context['user'] !== null) {
			/** @var UserInterface */
			$currentUser = $context['user'];

			if (method_exists($currentUser, 'getId')) {
				return $currentUser->getId();
			}

			if ($currentUser instanceof UserInterface) {
				if (method_exists($currentUser, 'getUserIdentifier')) {
					return $currentUser->getUserIdentifier();
				}

				return $currentUser->getUsername();
			}
		}

		return null;
	}
}
