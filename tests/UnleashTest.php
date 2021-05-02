<?php

namespace Stogon\UnleashBundle\Tests;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Stogon\UnleashBundle\Event\UnleashContextEvent;
use Stogon\UnleashBundle\Feature;
use Stogon\UnleashBundle\FeatureInterface;
use Stogon\UnleashBundle\Repository\FeatureRepository;
use Stogon\UnleashBundle\Strategy\StrategyInterface;
use Stogon\UnleashBundle\Unleash;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @coversDefaultClass \Stogon\UnleashBundle\Unleash
 */
class UnleashTest extends TestCase
{
	/**
	 * @covers ::__construct
	 * @covers ::getFeatures
	 */
	public function testGetFeatures(): void
	{
		$featureMock1 = $this->createMock(FeatureInterface::class);

		$featureRepositoryMock = $this->createMock(FeatureRepository::class);
		$featureRepositoryMock->expects($this->once())
			->method('getFeatures')
			->willReturn([
				$featureMock1,
			]);

		$unleash = new Unleash(
			$this->createMock(RequestStack::class),
			$this->createMock(TokenStorageInterface::class),
			$this->createMock(EventDispatcherInterface::class),
			$featureRepositoryMock,
			new ArrayIterator([]),
		);

		$features = $unleash->getFeatures();

		$this->assertCount(1, $features);
		$this->assertContainsOnlyInstancesOf(FeatureInterface::class, $features);
	}

	/**
	 * @covers ::__construct
	 * @covers ::getFeature
	 */
	public function testGetFeature(): void
	{
		$name = 'super_feature';

		$featureMock = $this->createMock(Feature::class);

		$featureRepositoryMock = $this->createMock(FeatureRepository::class);
		$featureRepositoryMock->expects($this->once())
			->method('getFeature')
			->with($name)
			->willReturn($featureMock);

		$unleash = new Unleash(
			$this->createMock(RequestStack::class),
			$this->createMock(TokenStorageInterface::class),
			$this->createMock(EventDispatcherInterface::class),
			$featureRepositoryMock,
			new ArrayIterator([]),
		);

		$feature = $unleash->getFeature($name);

		$this->assertSame($featureMock, $feature);
	}

	/**
	 * @covers ::__construct
	 * @covers ::getFeature
	 */
	public function testGetFeatureWithMissingFeature(): void
	{
		$name = 'random_feature';

		$featureRepositoryMock = $this->createMock(FeatureRepository::class);
		$featureRepositoryMock->expects($this->once())
			->method('getFeature')
			->with($name)
			->willReturn(null);

		$unleash = new Unleash(
			$this->createMock(RequestStack::class),
			$this->createMock(TokenStorageInterface::class),
			$this->createMock(EventDispatcherInterface::class),
			$featureRepositoryMock,
			new ArrayIterator([]),
		);

		$feature = $unleash->getFeature($name);

		$this->assertNull($feature);
	}

	/**
	 * @covers ::__construct
	 * @covers ::isFeatureEnabled
	 * @covers \Stogon\UnleashBundle\Event\UnleashContextEvent::__construct
	 * @covers \Stogon\UnleashBundle\Event\UnleashContextEvent::getPayload
	 */
	public function testIsFeatureEnabledWithoutDefaultValueWithAuthenticated(): void
	{
		$featureName = 'random_feature';

		$featureMock = $this->createMock(Feature::class);
		$featureMock->expects($this->once())
			->method('getStrategies')
			->willReturn([
				[
					'name' => 'userWithId',
					'parameters' => [
						'userIds' => 'admin,user1,user2',
					],
				],
			]);

		$featureRepositoryMock = $this->createMock(FeatureRepository::class);
		$featureRepositoryMock->expects($this->once())
			->method('getFeature')
			->with($featureName)
			->willReturn($featureMock);

		$strategyMock = $this->createMock(StrategyInterface::class);
		$strategyMock->expects($this->once())
			->method('isEnabled')
			->willReturn(true);

		$strategies = [
			'userWithId' => $strategyMock,
		];

		$requestMock = $this->createMock(Request::class);

		$requestStackMock = $this->createMock(RequestStack::class);
		$requestStackMock->expects($this->once())
			->method('getMasterRequest')
			->willReturn($requestMock);

		$userMock = $this->createMock(UserInterface::class);

		$tokenMock = $this->createMock(TokenInterface::class);
		$tokenMock->expects($this->once())
			->method('isAuthenticated')
			->willReturn(true);
		$tokenMock->expects($this->once())
			->method('getUser')
			->willReturn($userMock);

		$tokenStorageMock = $this->createMock(TokenStorageInterface::class);
		$tokenStorageMock->expects($this->once())
			->method('getToken')
			->willReturn($tokenMock);

		$eventMock = $this->createMock(UnleashContextEvent::class);

		$eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
		$eventDispatcherMock->expects($this->once())
			->method('dispatch')
			->with($this->isInstanceOf(UnleashContextEvent::class))
			->willReturn($eventMock);

		$unleash = new Unleash(
			$requestStackMock,
			$tokenStorageMock,
			$eventDispatcherMock,
			$featureRepositoryMock,
			new ArrayIterator($strategies),
		);

		$this->assertTrue($unleash->isFeatureEnabled($featureName));
	}
}
