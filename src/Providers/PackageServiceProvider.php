<?php

namespace Hexadog\TranslationManager\Providers;

use Hexadog\TranslationManager\Console\Commands;
use Hexadog\TranslationManager\Facades\TranslationManager as TranslationManagerFacade;
use Hexadog\TranslationManager\Finder;
use Hexadog\TranslationManager\Parser;
use Hexadog\TranslationManager\TranslationManager;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ReflectionClass;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Our root directory for this package to make traversal easier.
     */
    public const PACKAGE_DIR = __DIR__.'/../../';

    /**
     * Name for this package to publish assets.
     */
    public const PACKAGE_NAME = 'translation-manager';

    /**
     * Pblishers list.
     */
    protected $publishers = [];

    /**
     * Bootstrap the application events.
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
     * Get Package absolute path.
     *
     * @param string $path
     */
    protected function getPath($path = '')
    {
        // We get the child class
        $rc = new ReflectionClass(get_class($this));

        return dirname($rc->getFileName()).'/../../'.$path;
    }

    /**
     * Get Module normalized namespace.
     *
     * @param mixed $prefix
     */
    protected function getNormalizedNamespace($prefix = '')
    {
        return Str::start(Str::lower(self::PACKAGE_NAME), $prefix);
    }

    /**
     * Bootstrap our Configs.
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
        if ($this->app->runningInConsole() || 'testing' == config('app.env')) {
            $this->commands([
                Commands\MissingCommand::class,
                Commands\UnusedCommand::class,
            ]);
        }
    }

    /**
     * Bootstrap our Publishers.
     */
    protected function strapPublishers()
    {
        $configPath = $this->getPath('config');

        $this->publishes([
            "{$configPath}/config.php" => config_path($this->getNormalizedNamespace().'.php'),
        ], 'config');
    }
}
