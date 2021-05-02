<?php

namespace Stogon\UnleashBundle\Tests;

use PHPUnit\Framework\TestCase;
use Stogon\UnleashBundle\Feature;
use Stogon\UnleashBundle\Strategy\StrategyInterface;

/**
 * @coversDefaultClass \Stogon\UnleashBundle\Feature
 */
class FeatureTest extends TestCase
{
	/**
	 * @covers ::__construct
	 * @covers ::getName
	 * @covers ::getDescription
	 * @covers ::isEnabled
	 * @covers ::isDisabled
	 * @covers ::getStrategies
	 */
	public function testConstruct(): void
	{
		$strategies = [
			$this->createMock(StrategyInterface::class),
		];

		$feature = new Feature(
			'awesome_feature',
			'My awesome feature !',
			true,
			$strategies
		);

		$this->assertEquals('awesome_feature', $feature->getName());
		$this->assertEquals('My awesome feature !', $feature->getDescription());
		$this->assertTrue($feature->isEnabled());
		$this->assertFalse($feature->isDisabled());
		$this->assertContainsOnlyInstancesOf(StrategyInterface::class, $feature->getStrategies());
	}
}
