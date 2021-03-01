<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Stogon\UnleashBundle\HttpClient\UnleashHttpClient;
use Stogon\UnleashBundle\Repository\FeatureRepository;
use Stogon\UnleashBundle\Twig\UnleashExtension;
use Stogon\UnleashBundle\Unleash;
use Stogon\UnleashBundle\UnleashInterface;

return function (ContainerConfigurator $configurator) {
	$services = $configurator->services();

	$services->set(UnleashHttpClient::class)
		->arg('$apiUrl', '%unleash.api_url%')
		->arg('$instanceId', '%unleash.instance_id%')
		->arg('$environment', '%unleash.environment%')
		->autowire(true)
	;

	$services->set(FeatureRepository::class)
		->arg('$httpClient', service(UnleashHttpClient::class))
		->arg('$cache', '%unleash.cache.service%')
		->arg('$ttl', '%unleash.cache.ttl%')
		->autowire(true)
		->autoconfigure(true)
	;

	$services->set(Unleash::class)
		->arg('$strategiesMapping', '%unleash.strategies%')
		->autowire(true)
	;

	$services->alias(UnleashInterface::class, Unleash::class);

	$services->set(UnleashExtension::class)
		->arg('$unleash', service(UnleashInterface::class))
		->tag('twig.extension')
	;
};
