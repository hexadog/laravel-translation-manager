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

To publish the config file to config/translation-manager.php run:
```shell
php artisan vendor:publish --provider="Hexadog\TranslationManager\Providers\PackageServiceProvider"
```

<!-- omit in toc -->
## Usage
Translation Manager has many features to help you working with translation

- [Configuration](#configuration)
- [Artisan Commands](#artisan-commands)
  - [Find unused translation](#find-unused-translation)
  - [Find missing translation](#find-missing-translation)

### Configuration
This is the default contents of the configuration:
```php
<?php

return [
    // Directories to search in.
	'directories' => [
		'app',
		'resources',
	],

	// File Extensions to search for.
	'extensions' => [
		'php',
		'js',
	],

	// Translation function names.
	// If your function name contains $ escape it using \$ .
	'functions' => [
		'__',
		'_t',
		'@lang',
	],

	// Indicates weather you need to sort the translations alphabetically
	// by original strings (keys).
	// It helps navigate a translation file and detect possible duplicates.
	'sort-keys' => true,
];
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

<!-- omit in toc -->
## License
Laravel Translation Manager is open-sourced software licensed under the [MIT license](LICENSE).