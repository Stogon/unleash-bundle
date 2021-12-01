<?php

namespace Stogon\UnleashBundle\Tests\Strategy;

use PHPUnit\Framework\TestCase;
use Stogon\UnleashBundle\Strategy\UserWithIdStrategy;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @coversDefaultClass \Stogon\UnleashBundle\Strategy\UserWithIdStrategy
 */
class UserWithIdStrategyTest extends TestCase
{
	/**
	 * @dataProvider usernameProvider
	 * @covers ::isEnabled
	 */
	public function testIsEnabledWithUserIdentifier(array $parameters, string $username, bool $expected): void
	{
		$userMock = $this->createMock(User::class);
		if (method_exists(UserInterface::class, 'getUsername')) {
			$userMock->method('getUsername')->willReturn($username);
		} else {
			$userMock->method('getUserIdentifier')->willReturn($username);
		}

		$context = [
			'user' => $userMock,
		];

		$strategy = new UserWithIdStrategy();

		$this->assertEquals($expected, $strategy->isEnabled($parameters, $context));
	}

	public function usernameProvider(): array
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

	/**
	 * @dataProvider idProvider
	 * @covers ::isEnabled
	 */
	public function testIsEnabledWithId(array $parameters, $id, bool $expected): void
	{
		$userMock = $this->createMock(User::class);
		$userMock->method('getId')->willReturn($id);

		$context = [
			'user' => $userMock,
		];

		$strategy = new UserWithIdStrategy();

		$this->assertEquals($expected, $strategy->isEnabled($parameters, $context));
	}

	public function idProvider(): array
	{
		return [
			[
				[
					'userIds' => '1,2,3',
				],
				'1',
				true,
			],
			[
				[
					'userIds' => '1,2,3',
				],
				2,
				true,
			],
			[
				[
					'userIds' => '1,2,3',
				],
				'test',
				false,
			],
		];
	}
}
