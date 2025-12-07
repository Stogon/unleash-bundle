<?php

namespace Stogon\UnleashBundle\Strategy;

use Stogon\UnleashBundle\Helper\ValueNormalizer;

class GradualRolloutRandomStrategy implements StrategyInterface
{
	public function isEnabled(array $parameters = [], array $context = [], mixed ...$args): bool
	{
		$percentage = intval($parameters['percentage'] ?? 0);
		$groupId = trim($parameters['groupId'] ?? '');
		$randomId = sprintf('%s', mt_rand(1, 100));

		$randomIdValue = ValueNormalizer::build($randomId, $groupId);

		return $percentage > 0 && $randomIdValue <= $percentage;
	}
}
