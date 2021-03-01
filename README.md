# Unleash Bundle

TODO

## Installation

```
composer require stogon/unleash-bundle
```

## Configurations

Full configurations example:

```yaml
# config/packages/unleash.yaml
unleash:
	api_url: 'https://gitlab.com/api/v4/feature_flags/unleash/<project_id>'
	instance_id: '<some ID>'
	environment: '%kernel.environment%'
	cache:
		enabled: true
		service: '@cache.app'
		ttl: 15
	strategies:
		default: Stogon\UnleashBundle\Strategy\DefaultStrategy
		userWithId: Stogon\UnleashBundle\Strategy\UserWithIdStrategy
```

TODO: Add definitions for each settings.

## Usage

TODO: Add usage example.

## Testing

Simply run :

```
composer run test
```

or

```
./vendor/bin/phpunit
```

## Contributing

TODO:
