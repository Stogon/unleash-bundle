<?php

namespace Stogon\UnleashBundle\Tests\Strategy\Fixtures;

class SimpleUser
{
	public function __construct(private readonly int|string $id)
	{
	}

	public function getId(): int|string
	{
		return $this->id;
	}
}
