<?php

namespace Hexadog\TranslationManager\Providers;

use ReflectionClass;
use Illuminate\Support\Str;
use Illuminate\Routing\Router;
use Illuminate\Filesystem\Filesystem;
use Hexadog\TranslationManager\Finder;
use Hexadog\TranslationManager\Parser;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Hexadog\TranslationManager\Console\Commands;
use Illuminate\Contracts\Translation\Translator;
use Hexadog\TranslationManager\TranslationManager;
use Hexadog\TranslationManager\Facades\TranslationManager as TranslationManagerFacade;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Our root directory for this package to make traversal easier
     */
    const PACKAGE_DIR = __DIR__ . '/../../';

    /**
     * Name for this package to publish assets
     */
    const PACKAGE_NAME = 'translation-manager';

    /**
     * Pblishers list
     */
    protected $publishers = [];

    /**
     * Get Package absolute path
     *
     * @param string $path
     * @return void
     */
    protected function getPath($path = '')
    {
        // We get the child class
        $rc = new ReflectionClass(get_class($this));

        return dirname($rc->getFileName()) . '/../../' . $path;
    }

    /**
     * Get Module normalized namespace
     *
     * @return void
     */
    protected function getNormalizedNamespace($prefix = '')
    {
        return Str::start(Str::lower(self::PACKAGE_NAME), $prefix);
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->strapPublishers();
        $this->strapCommands();
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->registerConfigs();

        $this->app->singleton(TranslationManager::class, function () {
            return new TranslationManager(
                app(Translator::class),
                app(Filesystem::class),
                new Finder(),
                new Parser()
            );
        });

        AliasLoader::getInstance()->alias('TranslationManager', TranslationManagerFacade::class);
    }

    /**
     * Bootstrap our Configs
     */
    protected function registerConfigs()
    {
        $configPath = $this->getPath('config');

        $this->mergeConfigFrom(
            "{$configPath}/config.php",
            $this->getNormalizedNamespace()
        );
    }

    protected function strapCommands()
    {
        if ($this->app->runningInConsole() || config('app.env') == 'testing') {
            $this->commands([
                Commands\MissingCommand::class,
                Commands\UnusedCommand::class
            ]);
        }
    }

    /**
     * Bootstrap our Publishers
     */
    protected function strapPublishers()
    {
        $configPath = $this->getPath('config');

        $this->publishes([
            "{$configPath}/config.php" => config_path($this->getNormalizedNamespace() . '.php'),
        ], 'config');
    }
}
