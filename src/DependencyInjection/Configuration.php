<?php

namespace Stogon\UnleashBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
	public function getConfigTreeBuilder(): TreeBuilder
	{
		$treeBuilder = new TreeBuilder('unleash_bundle');

		$rootNode = $treeBuilder->getRootNode();

		// @phpstan-ignore-next-line
		$rootNode
			->fixXmlConfig('unleash')
			->children()
				->scalarNode('api_url')
					->info('Unleash API endpoint. URL Must end with a slash !')
					->example('https://gitlab.com/api/v4/feature_flags/unleash/<project_id>/')
					->isRequired()
					->cannotBeEmpty()
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
			->end()
		;

		return $treeBuilder;
	}
}
