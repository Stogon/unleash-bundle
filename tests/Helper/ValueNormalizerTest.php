<?php

namespace Stogon\UnleashBundle\Tests\Helper;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Stogon\UnleashBundle\Helper\ValueNormalizer;

#[CoversClass(ValueNormalizer::class)]
class ValueNormalizerTest extends TestCase
{
	public static function valueProvider(): \Iterator
	{
		yield 'with numeric ID, no group' => [
			'1', // id
			'', // groupId
			100, // normalizer
			1, // min
			7, // expected
		];
		yield 'with UUID, no group' => [
			'801a6d2d-6467-49bd-81e8-263677daa5f1', // id
			'', // groupId
			100, // normalizer
			1, // min
			31, // expected
		];
		yield 'with random chars, no group' => [
			'.+"*ç%&/()=', // id
			'', // groupId
			100, // normalizer
			1, // min
			30, // expected
		];

		yield 'with numeric ID, defined group' => [
			'1', // id
			'admin', // groupId
			100, // normalizer
			1, // min
			13, // expected
		];
		yield 'with UUID, defined group' => [
			'801a6d2d-6467-49bd-81e8-263677daa5f1', // id
			'admin', // groupId
			100, // normalizer
			1, // min
			28, // expected
		];
		yield 'with random chars, defined group' => [
			'.+"*ç%&/()=', // id
			'admin', // groupId
			100, // normalizer
			1, // min
			31, // expected
		];
	}

	#[DataProvider('valueProvider')]
	public function testBuild(string $id, string $groupId, int $normalizer, int $min, int $expected): void
	{
		$value = ValueNormalizer::build($id, $groupId, $normalizer, $min);

		$this->assertGreaterThan($min, $value);
		$this->assertLessThan($normalizer, $value);
		$this->assertSame($expected, $value);
	}
}
