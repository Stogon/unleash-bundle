# Unleash Bundle

An [Unleash](https://docs.getunleash.io/) bundle for Symfony applications.

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

To use the client, simply inject `Stogon\UnleashBundle\UnleashInterface` into your service and use it like this:

```php
<?php

namespace App\Controller;

use Stogon\UnleashBundle\UnleashInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(UnleashInterface $unleash): Response
    {
		if ($unleash->isFeatureEnabled('my_awesome_feature')) {
			// do something awesome !
		}

		if ($unleash->isFeatureDisabled('my_other_feature')) {
			// do something else
		}

        return $this->render('home/index.html.twig');
    }
}
```

### Twig

The bundle also provide Twig functions to check if a feature is enabled/disabled for the current user:

```twig
{# Check if a feature is enabled for current user #}
{%- if is_feature_enabled('my_awesome_feature') -%}
	<div class="alert alert-success" role="alert">
		The <code>my_awesome_feature</code> feature is enabled for current user !
	</div>
{%- else -%}
	<div class="alert alert-warning" role="alert">
		The <code>my_awesome_feature</code> feature is disabled for current user !
	</div>
{%- endif -%}

{# Check if a feature is disabled for current user #}
{%- if is_feature_disabled('my_awesome_feature') -%}
	<div class="alert alert-success" role="alert">
		The <code>my_awesome_feature</code> feature is disabled for current user !
	</div>
{%- else -%}
	<div class="alert alert-warning" role="alert">
		The <code>my_awesome_feature</code> feature is enabled for current user !
	</div>
{%- endif -%}
```

## Strategies

TODO: Add list of default strategies

### Add a custom strategy

TODO:

### Add additional context to strategies

TODO:

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
