<?php

namespace Stogon\UnleashBundle\DependencyInjection;

use Stogon\UnleashBundle\Repository\FeatureRepository;
use Stogon\UnleashBundle\Strategy\StrategyInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class UnleashExtension extends Extension implements PrependExtensionInterface
{
	public function load(array $configs, ContainerBuilder $container)
	{
		$loader = new PhpFileLoader(
			$container,
			new FileLocator(__DIR__.'/../../config/')
		);

		$loader->load('services.php');

		$container->registerForAutoconfiguration(StrategyInterface::class)->addTag('unleash.strategy');

		$definition = $container->getDefinition(FeatureRepository::class);
		$definition->replaceArgument('$cache', new Reference($container->getParameter('unleash.cache.service')));
	}

	public function prepend(ContainerBuilder $container)
	{
		$configuration = new Configuration();

		$config = $this->processConfiguration($configuration, $container->getExtensionConfig($this->getAlias()));

		$container->setParameter('unleash.api_url', $config['api_url']);
		$container->setParameter('unleash.instance_id', $config['instance_id']);
		$container->setParameter('unleash.environment', $config['environment']);
		$container->setParameter('unleash.cache.service', $config['cache']['service']);
		$container->setParameter('unleash.cache.ttl', $config['cache']['enabled'] ? $config['cache']['ttl'] : 0);

		$container->prependExtensionConfig('framework', [
			'http_client' => [
				'scoped_clients' => [
					'unleash.client' => [
						'base_uri' => '%unleash.api_url%',
						'headers' => [
							'Accept' => 'application/json',
							'UNLEASH-APPNAME' => '%unleash.environment%',
							'UNLEASH-INSTANCEID' => '%unleash.instance_id%',
						],
					],
				],
			],
		]);

		if ($config['cache']['enabled'] && $config['cache']['service'] === null) {
			$container->prependExtensionConfig('framework', [
				'cache' => [
					'pools' => [
						'cache.unleash.strategies' => null,
					],
				],
			]);

			$config['cache']['service'] = 'cache.unleash.strategies';

			$container->setParameter('unleash.cache.service', $config['cache']['service']);
		} else {
			$container->setParameter('unleash.cache.service', 'cache.app');
		}
	}

	public function getAlias()
	{
		return 'unleash';
	}
}
