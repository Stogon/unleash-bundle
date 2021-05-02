# Unleash Bundle

[![Packagist](https://img.shields.io/packagist/v/stogon/unleash-bundle.svg?style=for-the-badge)](https://packagist.org/packages/stogon/unleash-bundle)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/stogon/unleash-bundle.svg?style=for-the-badge)](https://packagist.org/packages/stogon/unleash-bundle)
[![License](https://img.shields.io/github/license/Stogon/unleash-bundle.svg?style=for-the-badge)](https://packagist.org/packages/stogon/unleash-bundle)

An [Unleash](https://docs.getunleash.io/) bundle for Symfony applications.

This provide an easy way to implement **feature flags** using [Gitlab Feature Flags Feature](https://docs.gitlab.com/ee/operations/feature_flags.html).

*Inspired by [minds/unleash-client-php](https://gitlab.com/minds/unleash-client-php) and [mikefrancis/laravel-unleash](https://github.com/mikefrancis/laravel-unleash).*

## Installation

```
composer require stogon/unleash-bundle
```

## Configurations

Full configurations example:

```yaml
# config/packages/unleash.yaml
unleash:
    # The full URL to your unleash-server instance.
    # Example with the "feature_flags" feature from Gitlab.com : https://gitlab.com/api/v4/feature_flags/unleash/<project_id>
    api_url: 'https://gitlab.com/api/v4/feature_flags/unleash/<project_id>'

    # Instance ID of your unleash application.
    # Example : VPQgqIdAxQyXY96d6oWj
    instance_id: '<some ID>'

    # Unleash application name.
    # For Gitlab feature flags, it can the the environment name.
    # default: '%kernel.environment%'
    environment: '%kernel.environment%'

    cache:
        # Enable caching of features fetched from Unleash server.
        # default: true
        enabled: true
        # Service ID to use for caching (must be a cache pool)
        # default: '%unleach.cache.service%' (which resolve to '@cache.unleash.strategies' service)
        service: '@cache.app'
        # The period of time from the present after which the item MUST be considered expired in the cache in seconds
        # default: 15
        ttl: 15
```

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

If you want to add additional data to the context passed to resolved strategy (`$context` parameter of the `Stogon\UnleashBundle\Strategy\StrategyInterface::isEnabled` method), you can implement an event listener/subscriber to react to the `Stogon\UnleashBundle\Event\UnleashContextEvent` event.

Example:
```php
<?php

namespace App\EventSubscriber;

use Stogon\UnleashBundle\Event\UnleashContextEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UnleashContextSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            UnleashContextEvent::class => ['onUnleashContextEvent'],
        ];
    }

    public function onUnleashContextEvent(UnleashContextEvent $event): void
    {
        // Get the original payload as an array;
        $payload = $event->getPayload();

        // Set some custom data
        $payload['awesome_data'] = 'amazing';

        // Update payload
        $event->setPayload($payload);
    }
}
```

## Testing

Simply run :

```
composer run test
```

or

```
$ ./vendor/bin/phpunit
$ ./vendor/bin/phpstan analyse
$ ./vendor/bin/php-cs-fixer fix --config=.php_cs.dist
```

## Contributing

TODO:

## Special thanks

Thanks to [@lastguest](https://github.com/lastguest) for his implementation of the `Murmur::hash3_int` ([https://github.com/lastguest/murmurhash-php](https://github.com/lastguest/murmurhash-php/blob/master/src/lastguest/Murmur.php))
