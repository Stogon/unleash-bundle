<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

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

return function (ContainerConfigurator $configurator) {
	$services = $configurator->services();

	$services->instanceof(StrategyInterface::class)->tag('unleash.strategy');

	$services->set(UnleashHttpClient::class)
		->arg('$apiUrl', '%unleash.api_url%')
		->arg('$instanceId', '%unleash.instance_id%')
		->arg('$environment', '%unleash.environment%')
		->autowire(true)
	;

	// Strategies definitions
	$services->set(DefaultStrategy::class)->tag('unleash.strategy', ['activation_name' => 'default']);
	$services->set(UserWithIdStrategy::class)->tag('unleash.strategy', ['activation_name' => 'userWithId']);
	$services->set(FlexibleRolloutStrategy::class)->tag('unleash.strategy', ['activation_name' => 'flexibleRollout']);
	$services->set(GradualRolloutUserIdStrategy::class)->tag('unleash.strategy', ['activation_name' => 'gradualRolloutUserId']);
	$services->set(GradualRolloutSessionIdStrategy::class)->tag('unleash.strategy', ['activation_name' => 'gradualRolloutSessionId']);
	$services->set(GradualRolloutRandomStrategy::class)->tag('unleash.strategy', ['activation_name' => 'gradualRolloutRandom']);

	$services->set(FeatureRepository::class)
		->arg('$httpClient', \function_exists('Symfony\Component\DependencyInjection\Loader\Configurator\service') ? service(UnleashHttpClient::class) : ref(UnleashHttpClient::class))
		->arg('$cache', '%unleash.cache.service%')
		->arg('$ttl', '%unleash.cache.ttl%')
		->autowire(true)
		->autoconfigure(true)
	;

	$services->set(Unleash::class)
		->arg('$strategiesMapping', tagged_iterator('unleash.strategy', 'activation_name'))
		->autowire(true)
	;

	$services->alias(UnleashInterface::class, Unleash::class);

	$services->set(UnleashExtension::class)
		->arg('$unleash', \function_exists('Symfony\Component\DependencyInjection\Loader\Configurator\service') ? service(UnleashInterface::class) : ref(UnleashInterface::class))
		->tag('twig.extension')
	;
};
