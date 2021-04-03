<?php

namespace Stogon\UnleashBundle\Strategy;

use Stogon\UnleashBundle\Helper\ValueNormalizer;
use Symfony\Component\HttpFoundation\Request;

class GradualRolloutSessionIdStrategy implements StrategyInterface
{
	public function isEnabled(array $parameters = [], array $context = [], ...$args): bool
	{
		$percentage = intval($parameters['percentage'] ?? 0);
		$groupId = trim($parameters['groupId'] ?? '');
		$sessionId = trim($this->getSessionId($context) ?? '');

		if (!$sessionId) {
			return false;
		}

		$sessionIdValue = ValueNormalizer::build($sessionId, $groupId);

		return $percentage > 0 && $sessionIdValue <= $percentage;
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
