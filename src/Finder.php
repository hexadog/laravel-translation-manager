<?php

namespace Hexadog\TranslationManager;

use Hexadog\TranslationManager\Contracts\Finder as ContractsFinder;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Finder\Finder as SymfonyFinder;

class Finder implements ContractsFinder
{
    /**
     * Directories to search in.
     *
     * @var array
     */
    protected $directories;

    /**
     * File Extensions to search for.
     *
     * @var array
     */
    protected $extensions;

    /**
     * Finder constructor.
     */
    public function __construct()
    {
        $this->directories = Config::get('translation-manager.directories', ['app', 'resources', 'lang']);
        $this->extensions = Config::get('translation-manager.extensions', ['php', 'js']);
    }

    /**
     * Find all files that can contain translatable strings.
     *
     * @param null|mixed $directories
     * @param null|mixed $extensions
     *
     * @return null|SymfonyFinder
     */
    public function find($directories = null, $extensions = null)
    {
        $files = collect();

        $directories = is_null($directories) ? $this->directories : (is_array($directories) ? $directories : [$directories]);
        $extensions = is_null($extensions) ? $this->extensions : (is_array($extensions) ? $extensions : [$extensions]);

        foreach ($directories as $directory) {
            $files = $files->merge(app('files')->allFiles($directory));
        }

        return $files->filter(function ($file) use ($extensions) {
            return in_array($file->getExtension(), $extensions);
        });
    }
}
