<?php

namespace Stogon\UnleashBundle;

class Feature implements FeatureInterface
{
	public function __construct(
		private readonly string $name,
		private readonly string $description,
		private readonly bool $enabled,
		protected array $stategies = []
	) {
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getDescription(): string
	{
		return $this->description;
	}

	public function isEnabled(): bool
	{
		return $this->enabled;
	}

	public function isDisabled(): bool
	{
		return !$this->enabled;
	}

	public function getStrategies(): array
	{
		return $this->stategies;
	}
}
