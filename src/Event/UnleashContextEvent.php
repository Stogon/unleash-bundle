<?php

namespace Stogon\UnleashBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class UnleashContextEvent extends Event
{
	public function __construct(private array $payload = [])
	{
	}

	public function getPayload(): array
	{
		return $this->payload;
	}

	public function setPayload(array $payload): self
	{
		$this->payload = $payload;

		return $this;
	}
}
