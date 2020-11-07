<p align="center"><img src="https://i.ibb.co/RbnsDrr/logo.png"></p>

<p align="center">
    <a href="https://packagist.org/packages/hexadog/laravel-translation-manager">
        <img src="https://poser.pugx.org/hexadog/laravel-translation-manager/v" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/hexadog/laravel-translation-manager">
        <img src="https://poser.pugx.org/hexadog/laravel-translation-manager/downloads" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/hexadog/laravel-themes-manager">
        <img src="https://poser.pugx.org/hexadog/laravel-translation-manager/license" alt="License">
    </a>
</p>

<!-- omit in toc -->
## Introduction
<code>hexadog/laravel-translation-manager</code> is a Laravel package to help you manage your application translation.

<!-- omit in toc -->
## Installation
This package requires PHP 7.3 and Laravel 7.0 or higher.

To get started, install Translation Manager using Composer:
```shell
composer require hexadog/laravel-translation-manager
```

The package will automatically register its service provider.

To publish the config file to config/themes-manager.php run:
```shell
php artisan vendor:publish --provider="Hexadog\TranslationManager\Providers\PackageServiceProvider"
```

<!-- omit in toc -->
## Usage
Translation Manager has many features to help you working with translation

- [Configuration](#configuration)
- [Basic usage](#basic-usage)
- [Artisan Commands](#artisan-commands)
  - [Find unused translation](#find-unused-translation)
  - [Find missing translation](#find-missing-translation)

### Configuration
This is the default contents of the configuration:
```php
<?php

return [
    
];
```

### Basic usage
There is multiple ways to work with Themes Manager. You can either set a new theme manually, using Web Middleware or Route Middleware.

Use the following method to set a theme manually at any time (in your controller for example):
```php
ThemesManager::set('one');
```

### Artisan Commands
This package provides some artisan commands in order to manage themes.

#### Find unused translation
Find all unused translation in your app
```shell
php artisan translation:unused
```

Find all unused translation in your app for specifig namespace
```shell
php artisan translation:unused --namespace=hexadog
```

Find all unused translation in your app for specifig language
```shell
php artisan translation:unused --lang=fr
```


#### Find missing translation
Find all missing translation in your app
```shell
php artisan translation:missing
```

Find all missing translation in your app for specifig namespace
```shell
php artisan translation:missing --namespace=hexadog
```

Find all missing translation in your app for specifig language
```shell
php artisan translation:missing --lang=fr
```

Automatically fix all missing translation found in your app
```shell
php artisan translation:missing --fix
```

<!-- omit in toc -->
## Related projects
- [Laravel Theme Installer](https://github.com/hexadog/laravel-theme-installer): Composer plugin to install `laravel-theme` packages outside vendor directory .

<!-- omit in toc -->
## Credits
- Logo made by [DesignEvo free logo creator](https://www.designevo.com/logo-maker/)

<!-- omit in toc -->
## License
Laravel Translation Manager is open-sourced software licensed under the [MIT license](LICENSE).