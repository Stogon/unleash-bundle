<?php

namespace Stogon\UnleashBundle\Tests\Strategy;

use PHPUnit\Framework\TestCase;
use Stogon\UnleashBundle\Strategy\UserWithIdStrategy;
use Stogon\UnleashBundle\Tests\Strategy\Fixtures\SimpleUser;
use Stogon\UnleashBundle\Tests\Strategy\Fixtures\User;

#[\PHPUnit\Framework\Attributes\CoversClass(UserWithIdStrategy::class)]
class UserWithIdStrategyTest extends TestCase
{
	#[\PHPUnit\Framework\Attributes\DataProvider('usernameProvider')]
	public function testIsEnabledWithUserIdentifier(array $parameters, string $username, bool $expected): void
	{
		$userMock = $this->createMock(User::class);
		$userMock->method('getUserIdentifier')->willReturn($username);

		$context = [
			'user' => $userMock,
		];

		$strategy = new UserWithIdStrategy();

		$this->assertEquals($expected, $strategy->isEnabled($parameters, $context));
	}

	public static function usernameProvider(): array
	{
		return [
			[
				[
					'userIds' => 'johndoe,john,doe',
				],
				'johndoe',
				true,
			],
			[
				[
					'userIds' => 'johndoe,john,doe',
				],
				'john',
				true,
			],
			[
				[
					'userIds' => 'johndoe,john,doe',
				],
				'test',
				false,
			],
		];
	}

	#[\PHPUnit\Framework\Attributes\DataProvider('idProvider')]
	public function testIsEnabledWithId(array $parameters, int|string $id, bool $expected): void
	{
		$userMock = $this->createMock(SimpleUser::class);
		$userMock->method('getId')->willReturn($id);

		$context = [
			'user' => $userMock,
		];

		$strategy = new UserWithIdStrategy();

		$this->assertEquals($expected, $strategy->isEnabled($parameters, $context));
	}

	public static function idProvider(): array
	{
		return [
			'with string identifier' => [
				[
					'userIds' => '1,2,3',
				],
				'1',
				true,
			],
			'with numeric identifier' => [
				[
					'userIds' => '1,2,3',
				],
				2,
				true,
			],
			'with unknown identifier' => [
				[
					'userIds' => '1,2,3',
				],
				'test',
				false,
			],
		];
	}
}
