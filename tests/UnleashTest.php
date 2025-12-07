<?php

namespace Stogon\UnleashBundle\Tests;

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

#[\PHPUnit\Framework\Attributes\CoversClass(Unleash::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(UnleashContextEvent::class)]
class UnleashTest extends TestCase
{
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
			new \ArrayIterator([]),
		);

		$features = $unleash->getFeatures();

		$this->assertCount(1, $features);
		$this->assertContainsOnlyInstancesOf(FeatureInterface::class, $features);
	}

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
			new \ArrayIterator([]),
		);

		$feature = $unleash->getFeature($name);

		$this->assertSame($featureMock, $feature);
	}

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
			new \ArrayIterator([]),
		);

		$feature = $unleash->getFeature($name);

		$this->assertNull($feature);
	}

	public function testIsFeatureEnabledWithoutDefaultValueWithUnauthenticated(): void
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
		$requestStackMock
			->method('getMainRequest')
			->willReturn($requestMock);

		$tokenStorageMock = $this->createMock(TokenStorageInterface::class);
		$tokenStorageMock->expects($this->once())
			->method('getToken')
			->willReturn(null);

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
			new \ArrayIterator($strategies),
		);

		$this->assertTrue($unleash->isFeatureEnabled($featureName));
	}

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
		$requestStackMock
			->method('getMainRequest')
			->willReturn($requestMock);

		$userMock = $this->createMock(UserInterface::class);

		$tokenMock = $this->createMock(TokenInterface::class);
		if (method_exists(TokenInterface::class, 'isAuthenticated')) {
			$tokenMock->method('isAuthenticated')
				->willReturn(true);
		}
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
			new \ArrayIterator($strategies),
		);

		$this->assertTrue($unleash->isFeatureEnabled($featureName));
	}

	public function testIsFeatureEnabledWithDiabledFeature(): void
	{
		$featureName = 'random_feature';

		$featureMock = $this->createMock(Feature::class);
		$featureMock->expects($this->never())->method('getStrategies');
		$featureMock->expects($this->once())
			->method('isDisabled')
			->willReturn(true);

		$featureRepositoryMock = $this->createMock(FeatureRepository::class);
		$featureRepositoryMock->expects($this->once())
			->method('getFeature')
			->with($featureName)
			->willReturn($featureMock);

		$strategyMock = $this->createMock(StrategyInterface::class);
		$strategyMock->expects($this->never())->method('isEnabled');

		$strategies = [
			'userWithId' => $strategyMock,
		];

		$requestStackMock = $this->createMock(RequestStack::class);

		$tokenStorageMock = $this->createMock(TokenStorageInterface::class);
		$tokenStorageMock->expects($this->never())->method('getToken');

		$eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
		$eventDispatcherMock->expects($this->never())->method('dispatch');

		$unleash = new Unleash(
			$requestStackMock,
			$tokenStorageMock,
			$eventDispatcherMock,
			$featureRepositoryMock,
			new \ArrayIterator($strategies),
		);

		$this->assertFalse($unleash->isFeatureEnabled($featureName));
	}
}
