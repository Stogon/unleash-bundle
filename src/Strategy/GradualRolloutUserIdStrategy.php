<?php

namespace Stogon\UnleashBundle\Strategy;

use Stogon\UnleashBundle\Helper\ValueNormalizer;
use Symfony\Component\Security\Core\User\UserInterface;

class GradualRolloutUserIdStrategy implements StrategyInterface
{
	public function isEnabled(array $parameters = [], array $context = [], mixed ...$args): bool
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
			$currentUser = $context['user'];
			if ($currentUser instanceof UserInterface) {
				return $currentUser->getUserIdentifier();
			}

			if (is_object($currentUser) && method_exists($currentUser, 'getId')) {
				return $currentUser->getId();
			}
		}

		return null;
	}
}
