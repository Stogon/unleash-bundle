<?php

namespace Stogon\UnleashBundle\Strategy;

use Stogon\UnleashBundle\Helper\ValueNormalizer;
use Symfony\Component\HttpKernel\Kernel;
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
			/** @var string|\Stringable|UserInterface */
			$currentUser = $context['user'];

			// This means user is anonymous
			if (is_string($currentUser)) {
				return null;
			}

			if (method_exists($currentUser, 'getId')) {
				return $currentUser->getId();
			}

			if ($currentUser instanceof UserInterface) {
				if (Kernel::VERSION_ID >= 50300 && method_exists($currentUser, 'getUserIdentifier')) {
					return $currentUser->getUserIdentifier();
				}

				// @phpstan-ignore-next-line
				return $currentUser->getUsername();
			}
		}

		return null;
	}
}
