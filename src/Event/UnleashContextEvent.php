<?php

namespace Stogon\UnleashBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class UnleashContextEvent extends Event
{
	private array $payload;

	public function __construct(array $payload = [])
	{
		$this->payload = $payload;
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
