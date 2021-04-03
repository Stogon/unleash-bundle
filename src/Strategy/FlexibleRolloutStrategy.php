<?php

namespace Stogon\UnleashBundle\Strategy;

use Stogon\UnleashBundle\Helper\ValueNormalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class FlexibleRolloutStrategy implements StrategyInterface
{
	public function isEnabled(array $parameters = [], array $context = [], ...$args): bool
	{
		$percentage = intval($parameters['rollout'] ?? 0);
		$stickiness = strtolower($parameters['stickiness'] ?? 'default');
		$groupId = trim($parameters['groupId'] ?? '');
		$userId = trim($this->getUserId($context) ?? '');
		$sessionId = trim($this->getSessionId($context) ?? '');
		$randomId = sprintf('%s', mt_rand(1, 100));

		switch ($stickiness) {
			case 'userid':
				$stickinessId = $userId;
				break;

			case 'sessionid':
				$stickinessId = $sessionId;
				break;

			case 'random':
				$stickinessId = $randomId;
				break;

			default:
				// Default strategy is to use available ID in this order: userId, sessionId and randomId.
				$stickinessId = $userId ?: $sessionId ?: $randomId;
				break;
		}

		if (!$stickinessId) {
			return false;
		}

		// Get percentage value for given ID and group ID
		$stickinessValue = ValueNormalizer::build($stickinessId, $groupId);

		return $percentage > 0 && $stickinessValue <= $percentage;
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
				return $currentUser->getUsername();
			}
		}

		return null;
	}

	protected function getSessionId(array $context): ?string
	{
		if (array_key_exists('request', $context) && $context['request'] !== null) {
			/** @var Request */
			$request = $context['request'];

			return $request->getSession()->getId();
		}

		return null;
	}
}
