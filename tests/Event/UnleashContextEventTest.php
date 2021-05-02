<?php

namespace Stogon\UnleashBundle\Tests\Event;

use PHPUnit\Framework\TestCase;
use Stogon\UnleashBundle\Event\UnleashContextEvent;

/**
 * @coversDefaultClass \Stogon\UnleashBundle\Event\UnleashContextEvent
 */
class UnleashContextEventTest extends TestCase
{
	/**
	 * @covers ::__construct
	 * @covers ::getPayload
	 */
	public function testConstruct(): void
	{
		$event = new UnleashContextEvent([
			'my_super_payload' => 'random_value',
		]);

		$this->assertArrayHasKey('my_super_payload', $event->getPayload());
		$this->assertContains('random_value', $event->getPayload());
	}

	/**
	 * @covers ::__construct
	 * @covers ::getPayload
	 * @covers ::setPayload
	 */
	public function testSetPayload(): void
	{
		$event = new UnleashContextEvent([
			'my_super_payload' => 'random_value',
		]);

		$event->setPayload([
			'new_payload' => 'random_data',
		]);

		$this->assertArrayNotHasKey('my_super_payload', $event->getPayload());
		$this->assertNotContains('random_value', $event->getPayload());
		$this->assertArrayHasKey('new_payload', $event->getPayload());
		$this->assertContains('random_data', $event->getPayload());
	}
}
