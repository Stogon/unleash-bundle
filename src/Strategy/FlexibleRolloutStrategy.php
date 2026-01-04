<?php

namespace Stogon\UnleashBundle\Strategy;

use Stogon\UnleashBundle\Helper\ValueNormalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class FlexibleRolloutStrategy implements StrategyInterface
{
	public function isEnabled(array $parameters = [], array $context = [], mixed ...$args): bool
	{
		$percentage = intval($parameters['rollout'] ?? 0);
		$stickiness = strtolower($parameters['stickiness'] ?? 'default');
		$groupId = trim($parameters['groupId'] ?? '');
		$userId = trim($this->getUserId($context) ?? '');
		$sessionId = trim($this->getSessionId($context) ?? '');
		$randomId = sprintf('%s', mt_rand(1, 100));

		$stickinessId = match ($stickiness) {
			'userid' => $userId,
			'sessionid' => $sessionId,
			'random' => $randomId,
			default => ($userId ?: $sessionId) ?: $randomId,
		};

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
