<?php

namespace Stogon\UnleashBundle\Tests\Strategy\Fixtures;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
	public function __construct(private $id, private readonly string $username)
	{
	}

	public function getId()
	{
		return $this->id;
	}

	public function getRoles(): array
	{
		return ['ROLE_USER'];
	}

	public function getPassword()
	{
		return 'thisIs_aSTRONG?Password';
	}

	public function getSalt()
	{
		return null;
	}

	public function eraseCredentials(): void
	{
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function getUserIdentifier(): string
	{
		return $this->username;
	}
}
