<?php

namespace Stogon\UnleashBundle\DependencyInjection;

use Stogon\UnleashBundle\Strategy\DefaultStrategy;
use Stogon\UnleashBundle\Strategy\StrategyInterface;
use Stogon\UnleashBundle\Strategy\UserWithIdStrategy;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
	public function getConfigTreeBuilder(): TreeBuilder
	{
		$treeBuilder = new TreeBuilder('unleash_bundle');

		$rootNode = $treeBuilder->getRootNode();

		$rootNode
			// @phpstan-ignore-next-line
			->fixXmlConfig('unleash')
			->children()
				->scalarNode('api_url')
					->info('Unleash API endpoint')
					->isRequired()
					->cannotBeEmpty()
					->beforeNormalization()
						->ifString()
						// Add a trailing slash at the end of the URL
						->then(fn ($v) => rtrim($v, '/').'/')
					->end()
					->validate()
						->ifTrue(function ($value) {
							return filter_var($value, FILTER_VALIDATE_URL) === false;
						})
						->thenInvalid('Invalid URL given : %s')
					->end()
				->end()
				->scalarNode('instance_id')
					->info('Unleash instance ID')
					->isRequired()
					->cannotBeEmpty()
				->end()
				->scalarNode('environment')
					->info('Unleash application name. For Gitlab, it can be the environment name')
					->defaultValue('%kernel.environment%')
					->cannotBeEmpty()
				->end()
				->arrayNode('cache')
					->canBeEnabled()
					->info('Cache configurations for strategies')
					->addDefaultsIfNotSet()
					->children()
						->scalarNode('service')
							->defaultNull()
						->end()
						->floatNode('ttl')
							->min(0)
							->defaultValue(15)
						->end()
					->end()
				->end()
				->arrayNode('strategies')
					->info('Mapping of strategies name to strategy class implementation')
					->useAttributeAsKey('name')
					->defaultValue([
						'default' => DefaultStrategy::class,
						'userWithId' => UserWithIdStrategy::class,
					])
					->cannotBeEmpty()
					->scalarPrototype()->end()
					->validate()
						->ifTrue(function (array $strategies) {
							foreach ($strategies as $class) {
								$implements = @class_implements($class);

								if ($implements === false) {
									return true;
								}

								if (!in_array(StrategyInterface::class, class_implements($class), true)) {
									return true;
								}
							}
						})
						->thenInvalid('Configured strategies %s must implement "'.StrategyInterface::class.'" interface.')
					->end()
				->end()
			->end()
		;

		return $treeBuilder;
	}
}