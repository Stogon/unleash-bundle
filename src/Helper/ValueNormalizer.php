<?php

namespace Stogon\UnleashBundle\Helper;

use lastguest\Murmur;

class ValueNormalizer
{
	/**
	 * Normalizes a value using Murmur3 algorithm hash and a normalizer modulus.
	 * Returns a value from $min (default 1) to $normalizer (default 100) if
	 * ID is truthy, if not it returns $min - 1 (default 0).
	 */
	public static function build(string $id, string $groupId, int $normalizer = 100, int $min = 1): int
	{
		if (!$id) {
			return $min - 1;
		}

		return (Murmur::hash3_int("{$id}:{$groupId}") % $normalizer) + $min;
	}
}
