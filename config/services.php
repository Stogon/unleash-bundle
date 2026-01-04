<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Stogon\UnleashBundle\Cache\FeatureCacheWarmer;
use Stogon\UnleashBundle\Command\FetchFeaturesCommand;
use Stogon\UnleashBundle\Command\ListFeaturesCommand;
use Stogon\UnleashBundle\HttpClient\UnleashHttpClient;
use Stogon\UnleashBundle\Repository\FeatureRepository;
use Stogon\UnleashBundle\Strategy\DefaultStrategy;
use Stogon\UnleashBundle\Strategy\FlexibleRolloutStrategy;
use Stogon\UnleashBundle\Strategy\GradualRolloutRandomStrategy;
use Stogon\UnleashBundle\Strategy\GradualRolloutSessionIdStrategy;
use Stogon\UnleashBundle\Strategy\GradualRolloutUserIdStrategy;
use Stogon\UnleashBundle\Strategy\StrategyInterface;
use Stogon\UnleashBundle\Strategy\UserWithIdStrategy;
use Stogon\UnleashBundle\Twig\UnleashExtension;
use Stogon\UnleashBundle\Unleash;
use Stogon\UnleashBundle\UnleashInterface;

return function (ContainerConfigurator $configurator): void {
	$services = $configurator->services();

	$services->instanceof(StrategyInterface::class)->tag('unleash.strategy');

	$services->set(UnleashHttpClient::class)
		->arg('$apiUrl', '%unleash.api_url%')
		->arg('$instanceId', '%unleash.instance_id%')
		->arg('$environment', '%unleash.environment%')
		->autowire(true)
		->call('setLogger', [service('logger')->ignoreOnInvalid()])
		->tag('monolog.logger', ['channel' => 'unleash'])
	;

	// Strategies definitions
	$services->set(DefaultStrategy::class)->tag('unleash.strategy', ['activation_name' => 'default']);
	$services->set(UserWithIdStrategy::class)->tag('unleash.strategy', ['activation_name' => 'userWithId']);
	$services->set(FlexibleRolloutStrategy::class)->tag('unleash.strategy', ['activation_name' => 'flexibleRollout']);
	$services->set(GradualRolloutUserIdStrategy::class)->tag('unleash.strategy', ['activation_name' => 'gradualRolloutUserId']);
	$services->set(GradualRolloutSessionIdStrategy::class)->tag('unleash.strategy', ['activation_name' => 'gradualRolloutSessionId']);
	$services->set(GradualRolloutRandomStrategy::class)->tag('unleash.strategy', ['activation_name' => 'gradualRolloutRandom']);

	$services->set(FeatureRepository::class)
		->arg('$httpClient', service(UnleashHttpClient::class))
		->arg('$cache', '%unleash.cache.service%')
		->arg('$ttl', '%unleash.cache.ttl%')
		->autowire(true)
		->autoconfigure(true)
	;

	$services->set(Unleash::class)
		->arg('$strategiesMapping', tagged_iterator('unleash.strategy', 'activation_name'))
		->autowire(true)
		->tag('monolog.logger', ['channel' => 'unleash'])
	;

	$services->alias(UnleashInterface::class, Unleash::class);

	$services->set(UnleashExtension::class)
		->arg('$unleash', service(UnleashInterface::class))
		->tag('twig.extension')
	;

	$services->set(FeatureCacheWarmer::class)
		->arg('$featureRepository', service(FeatureRepository::class))
		->tag('kernel.cache_warmer', ['priority' => 0])
	;

	$services->set(FetchFeaturesCommand::class)
		->arg('$featureRepository', service(FeatureRepository::class))
		->tag('console.command')
	;

	$services->set(ListFeaturesCommand::class)
		->arg('$featureRepository', service(FeatureRepository::class))
		->tag('console.command')
	;
};
