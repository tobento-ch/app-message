# App Message

[Message](https://github.com/tobento-ch/service-message) manager for the app.

## Table of Contents

- [Getting Started](#getting-started)
    - [Requirements](#requirements)
- [Documentation](#documentation)
    - [App](#app)
    - [Message Boot](#message-boot)
        - [Creating Messages](#creating-messages)
        - [Translating Messages](#translating-messages)
        - [Logging Messages](#logging-messages)
- [Credits](#credits)
___

# Getting Started

Add the latest version of the app message project running this command.

```
composer require tobento/app-message
```

## Requirements

- PHP 8.0 or greater

# Documentation

## App

Check out the [**App Skeleton**](https://github.com/tobento-ch/app-skeleton) if you are using the skeleton.

You may also check out the [**App**](https://github.com/tobento-ch/app) to learn more about the app in general.

## Message Boot

The message boot does the following:

* configures the messages factory

```php
use Tobento\App\AppFactory;
use Tobento\Service\Message\MessagesFactoryInterface;

// Create the app
$app = (new AppFactory())->createApp();

// Add directories:
$app->dirs()
    ->dir(realpath(__DIR__.'/../'), 'root')
    ->dir(realpath(__DIR__.'/../app/'), 'app')
    ->dir($app->dir('app').'config', 'config', group: 'config')
    ->dir($app->dir('root').'public', 'public')
    ->dir($app->dir('root').'vendor', 'vendor');

// Adding boots
$app->boot(\Tobento\App\Message\Boot\Message::class);
$app->booting();

// Implemented interfaces:
$messagesFactory = $app->get(MessagesFactoryInterface::class);

// Run the app
$app->run();
```

### Creating Messages

Check out the [Message Service - Messages Factory](https://github.com/tobento-ch/service-message#messages-factory) section to learn more about creating messages.

### Translating Messages

Simply, install the [App Translation](https://github.com/tobento-ch/app-translation) bundle and boot the ```\Tobento\App\Translation\Boot\Translation::class```:

```
composer require tobento/app-translation
```

```php
use Tobento\App\AppFactory;

// Create the app
$app = (new AppFactory())->createApp();

// Add directories:
$app->dirs()
    ->dir(realpath(__DIR__.'/../'), 'root')
    ->dir(realpath(__DIR__.'/../app/'), 'app')
    ->dir($app->dir('app').'config', 'config', group: 'config')
    ->dir($app->dir('root').'public', 'public')
    ->dir($app->dir('root').'vendor', 'vendor');
    
// Adding boots
$app->boot(\Tobento\App\Translation\Boot\Translation::class);
$app->boot(\Tobento\App\Message\Boot\Message::class);

// Run the app
$app->run();
```

Messages will be translated based on the [Configured Translator Locale](https://github.com/tobento-ch/app-translation#configure-translator).

The configured [Message Translator Modifier](https://github.com/tobento-ch/service-message#translator) uses the ```*``` as resource name. Check out the [Translation Resources](https://github.com/tobento-ch/service-translation#resources) and [Translation Files Resources](https://github.com/tobento-ch/service-translation#files-resources) to learn more about it.

Check out the [Add Translation](https://github.com/tobento-ch/app-translation#add-translations) or [Migrate Translation](https://github.com/tobento-ch/app-translation#migrate-translations) section to learn how to add or migrate translations.

### Logging Messages

Simply, install the [App Logging](https://github.com/tobento-ch/app-logging) bundle and boot the ```\Tobento\App\Logging\Boot\Logging::class```:

```
composer require tobento/app-translation
```

```php
use Tobento\App\AppFactory;

// Create the app
$app = (new AppFactory())->createApp();

// Add directories:
$app->dirs()
    ->dir(realpath(__DIR__.'/../'), 'root')
    ->dir(realpath(__DIR__.'/../app/'), 'app')
    ->dir($app->dir('app').'config', 'config', group: 'config')
    ->dir($app->dir('root').'public', 'public')
    ->dir($app->dir('root').'vendor', 'vendor');
    
// Adding boots
$app->boot(\Tobento\App\Logging\Boot\Logging::class);
$app->boot(\Tobento\App\Message\Boot\Message::class);

// Run the app
$app->run();
```

On the [App Logging Config](https://github.com/tobento-ch/app-logging#logging-config) file define the logger used for messages:

```
/*
|--------------------------------------------------------------------------
| Aliases
|--------------------------------------------------------------------------
*/

'aliases' => [
    'messages' => 'daily',
],
```

# Credits

- [Tobias Strub](https://www.tobento.ch)
- [All Contributors](../../contributors)