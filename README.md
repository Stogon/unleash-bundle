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

Available strategies:

| Strategy name | Description |
|---|----|
| `default` | It is the simplest activation strategy and basically means "active for everyone". |
| `userWithId` | This strategy allows you to specify a list of user IDs that you want to expose the new feature for. (A user id may, of course, be an email if that is more appropriate in your system.) |
| `flexibleRollout` | A flexible rollout strategy which combines all gradual rollout strategies in to a single strategy (and will in time replace them) |
| `gradualRolloutUserId` | The `gradualRolloutUserId` strategy gradually activates a feature toggle for logged-in users. Stickiness is based on the user ID. The strategy guarantees that the same user gets the same experience every time across devices |
| `gradualRolloutSessionId` | Similar to `gradualRolloutUserId` strategy, this strategy gradually activates a feature toggle, with the exception being that the stickiness is based on the session IDs. This makes it possible to target all users (not just logged-in users), guaranteeing that a user will get the same experience within a session. |
| `gradualRolloutRandom` | The `gradualRolloutRandom` strategy randomly activates a feature toggle and has no stickiness. We have found this rollout strategy very useful in some scenarios, especially when we enable a feature which is not visible to the user. It is also the strategy we use to sample metrics and error reports. |

> For more informations, see https://docs.getunleash.io/docs/activation_strategy

### Add a custom strategy

If the existing strategies does not fill your needs, you can implement a custom strategy with your own logic.

First, you need to create a class which implements the `Stogon\UnleashBundle\Strategy\StrategyInterface`
```php
<?php

namespace App\Unleash\Strategy;

use Stogon\UnleashBundle\Strategy\StrategyInterface;

class MyCustomStrategy implements StrategyInterface
{
	public function isEnabled(array $parameters = [], array $context = [], ...$args): bool
	{
		// TODO: Implement your custom logic here.

		return false;
	}
}
```

Then you need to tag your custom strategy with the `unleash.strategy` tag and provide a `activation_name` for it.
```yaml
services:
	App\Unleash\Strategy\MyCustomStrategy:
		tags:
			- { name: unleash.strategy, activation_name: my_custom_activation_strategy }
```

> The `activation_name` must match the `strategy.name` value of your Unleash strategy !
see https://docs.getunleash.io/docs/activation_strategy

### Override an existing strategy

You can override an existing strategy simply by setting the `activation_name` of the tag
to the same [strategy name used here](#strategies).

Example:
```yaml
services:
	App\Unleash\Strategy\MyCustomStrategy:
		tags:
			- { name: unleash.strategy, activation_name: userWithId }
```

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
