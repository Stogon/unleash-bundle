<?php

namespace Stogon\UnleashBundle\Tests\Twig;

use PHPUnit\Framework\TestCase;
use Stogon\UnleashBundle\Twig\UnleashExtension;
use Stogon\UnleashBundle\UnleashInterface;

/**
 * @coversDefaultClass \Stogon\UnleashBundle\Twig\UnleashExtension
 */
class UnleashExtensionTest extends TestCase
{
	protected const FEATURES = [
		'awesome_feature' => true,
		'beta_feature' => false,
	];

	/**
	 * @covers ::__construct
	 * @covers ::isFeatureEnabled
	 */
	public function testIsFeatureEnabled(): void
	{
		$unleashMock = $this->createMock(UnleashInterface::class);
		$unleashMock->expects($this->once())
			->method('isFeatureEnabled')
			->with('awesome_feature', false)
			->willReturn(true);

		$extension = new UnleashExtension($unleashMock);

		$result = $extension->isFeatureEnabled('awesome_feature', false);

		$this->assertTrue($result);
	}

	/**
	 * @covers ::__construct
	 * @covers ::isFeatureEnabled
	 */
	public function testIsFeatureEnabledWithFallback(): void
	{
		$unleashMock = $this->createMock(UnleashInterface::class);
		$unleashMock->expects($this->once())
			->method('isFeatureEnabled')
			->with('beta_feature', true)
			->willReturn(true);

		$extension = new UnleashExtension($unleashMock);

		$result = $extension->isFeatureEnabled('beta_feature', true);

		$this->assertTrue($result);
	}

	/**
	 * @covers ::__construct
	 * @covers ::isFeatureDisabled
	 */
	public function testIsFeatureDisabled(): void
	{
		$unleashMock = $this->createMock(UnleashInterface::class);
		$unleashMock->expects($this->once())
			->method('isFeatureDisabled')
			->with('awesome_feature', false)
			->willReturn(true);

		$extension = new UnleashExtension($unleashMock);

		$result = $extension->isFeatureDisabled('awesome_feature', false);

		$this->assertTrue($result);
	}

	/**
	 * @covers ::__construct
	 * @covers ::isFeatureDisabled
	 */
	public function testIsFeatureDisabledWithFallback(): void
	{
		$unleashMock = $this->createMock(UnleashInterface::class);
		$unleashMock->expects($this->once())
			->method('isFeatureDisabled')
			->with('beta_feature', true)
			->willReturn(true);

		$extension = new UnleashExtension($unleashMock);

		$result = $extension->isFeatureDisabled('beta_feature', true);

		$this->assertTrue($result);
	}
}
