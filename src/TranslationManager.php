<?php

namespace Hexadog\TranslationManager;

use Hexadog\TranslationManager\Contracts\Finder;
use Hexadog\TranslationManager\Contracts\Parser;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\NamespacedItemResolver;
use Illuminate\Support\Str;

class TranslationManager extends NamespacedItemResolver
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;
    /**
     * Strings extractor.
     *
     * @var Extractor
     */
    private $extractor;

    /**
     * Files Finder.
     *
     * @var Finder
     */
    private $finder;

    /**
     * Supported languages.
     *
     * @var array
     */
    private $languages = ['en'];

    /**
     * Undocumented variable.
     *
     * @var [type]
     */
    private $translator;

    public function __construct(Translator $translator, Filesystem $files, Finder $finder, Parser $parser)
    {
        $this->files = $files;
        $this->finder = $finder;
        $this->extractor = new Extractor($this->finder, $parser);
        $this->translator = $translator;

        $this->addSupportedLanguage('en');
    }

    /**
     * Array of supported languages.
     *
     * ex: ['en', 'fr']
     *
     * @return array
     */
    public function getSupportedLanguages()
    {
        return $this->languages;
    }

    /**
     * Undocumented function.
     *
     * @param [type] $lang
     */
    public function addSupportedLanguage($lang)
    {
        if (!is_array($lang)) {
            $lang = [$lang];
        }

        foreach ($lang as $l) {
            if (!in_array($l, $this->languages)) {
                $this->languages[] = $l;
            }
        }
    }

    /**
     * Find missing translations for namespace(s) by language(s).
     *
     * @param string $namespaces
     * @param string $languages
     * @param mixed  $autoFix
     */
    public function findMissing($namespaces = null, $languages = null, $autoFix = false): array
    {
        $missingStrings = [];

        // Retreive all used strings
        $usedStrings = $this->extractor->extract();

        // If no language provided, search for all languages supported by application
        if (is_null($languages) || (is_array($languages) && !count($languages))) {
            $languages = $this->getSupportedLanguages();
        }

        $hints = $this->prepareNamespaces($namespaces);

        // Check if translation exists for each languages
        foreach ($languages as $language) {
            $missingStrings[$language] = [];
            foreach ($hints as $namespace => $path) {
                $missingStrings[$language][$namespace] = $this->sortIfEnabled($usedStrings->filter(function ($key) use ($language, $namespace) {
                    if ('' === $namespace || Str::startsWith($key, $namespace.'::')) {
                        return !$this->translator->hasForLocale($key, $language);
                    }

                    return false;
                })->mapWithKeys(function ($value, $key) use ($namespace) {
                    return [$key => '' !== $namespace ? preg_replace('/^'.$namespace.'::/', '', $value) : ''];
                })->toArray());
            }
        }

        return $this->arrayUniqueRecursive($missingStrings);
        // if ($autoFix) {
        //     foreach ($missingStrings as $lang => $namespaces) {
        //         foreach ($namespaces as $namespace => $strings) {
        //             foreach ($strings as $string) {
        //                 $filePath = $this->getTranslationFilePath($string, $lang);
        //                 $translatedStrings = require $filePath;

        //                 // Remove namespace if there is any
        //                 if ($pos = strpos($string, '::') !== false) {
        //                     $string = substr($string, $pos + 2, strlen($string));
        //                 }

        //                 // Remove first segment : it's the lang filename
        //                 $string = substr($string, strpos($string, '.') ? strpos($string, '.') + 1 : 0, strlen($string));

        //                 $translatedStrings = array_merge_recursive($translatedStrings, $this->undotStringToArray($string));

        //                 $this->writeTranslationsToFile($translatedStrings, $filePath);
        //             }
        //         }
        //     }
        // }
    }

    /**
     * Find all unused strings declared in resources files.
     *
     * @param string       $namespaces
     * @param array|string $languages
     */
    public function findUnused($namespaces = null, $languages = null, bool $autoClean = false): array
    {
        $unusedStrings = [];
        $usedStrings = $this->extractor->extract();

        // If no language provided, search for all languages supported by application
        if (is_null($languages) || (is_array($languages) && !count($languages))) {
            $languages = $this->getSupportedLanguages();
        }

        $hints = $this->prepareNamespaces($namespaces);

        // Filter used strings based on requested namespaces
        $strings = [];
        foreach ($hints as $namespace => $path) {
            // Filter used strings to only keep requested namespaces
            $usedStrings->each(function ($key) use ($namespace, &$strings) {
                if ('' === $namespace || Str::startsWith($key, $namespace.'::')) {
                    $strings[] = $key;
                }
            });
        }

        // check translation usage all supported languages
        foreach ($languages as $language) {
            $unusedStrings[$language] = [];
            foreach ($hints as $namespace => $path) {
                $unusedStrings[$language][$namespace] = [];
                $files = $this->finder->find("{$path}/{$language}", 'php');

                foreach ($files as $file) {
                    // Get all strings in namespace
                    $translations = include $file->getPathname();

                    foreach ($translations as $key => $value) {
                        $key = $file->getBasename('.php').'.'.$key;

                        if (is_array($value)) {
                            foreach (Arr::dot($value) as $k => $val) {
                                $searchKey = '' !== $namespace ? $namespace.'::'.$key.'.'.$k : $key.'.'.$k;
                                if (!in_array($searchKey, $strings)) {
                                    $unusedStrings[$language][$namespace][$key.'.'.$k] = $val;
                                }
                            }
                        } else {
                            $searchKey = '' !== $namespace ? $namespace.'::'.$key : $key;
                            if (!in_array($searchKey, $strings)) {
                                $unusedStrings[$language][$namespace][$key] = $value;
                            }
                        }
                    }
                }
            }
        }

        return $this->sortIfEnabled($unusedStrings);
    }

    /**
     * Prepare namespaces.
     *
     * @param array|string $namespaces
     */
    protected function prepareNamespaces($namespaces = null): Collection
    {
        $namespacesCollection = collect();

        $this->translator->addNamespace('hexadog', resource_path('lang'));

        // Get Translator namespaces
        $loader = $this->translator->getLoader();
        if ($loader) {
            foreach ($loader->namespaces() as $hint => $path) {
                $namespacesCollection->put($hint, $path);
            }
        }

        if (is_string($namespaces)) {
            $namespaces = explode(',', $namespaces);
        }

        $namespaces = collect($namespaces);

        if (count($namespaces)) {
            $namespacesCollection = $namespacesCollection->filter(function ($path, $namespace) use ($namespaces) {
                return $namespaces->contains($namespace);
            });
        } else {
            // Add default namespace
            $namespacesCollection->put('', resource_path('lang'));
        }

        // Return namespaces collection after removing non existing paths
        return $namespacesCollection->filter(function ($path) {
            return file_exists($path);
        });
    }

    /**
     * Find translation file from translatable key.
     *
     * @param string $key
     * @param string $locale
     *
     * @return string
     */
    protected function getTranslationFilePath($key, $locale = 'en')
    {
        $filePath = null;
        $loader = $this->translator->getLoader();

        [$namespace, $group, $item] = $this->parseKey($key);

        if (is_null($namespace) || '*' === $namespace) {
            // Search into default lang folder
            $hintPath = resource_path("lang/{$locale}");
        } else {
            $hints = $loader->namespaces();

            // Check if hint exists for namespace
            if (array_key_exists($namespace, $hints)) {
                $hintPath = Arr::get($hints, $namespace)."/{$locale}";
            } else {
                // TODO: are we sure we create file in default path ???
                $hintPath = resource_path("lang/{$locale}");
            }
        }

        $filePath = "{$hintPath}/{$group}.php";
        if (!$this->files->exists($filePath)) {
            // TODO: create file if not exists yet
            File::put($filePath, "<?php \n\nreturn [];");
        }

        return $filePath;
    }

    /**
     * Convert given string to associative.
     *
     * @param string $string
     * @param mixed  $value
     *
     * @return array
     */
    protected function undotStringToArray($value)
    {
        $array = [];

        if (false === strpos($value, '.')) {
            $array[$value] = $value;
        } else {
            $keys = explode('.', $value);
            $key = array_shift($keys);

            $array[$key] = $this->undotStringToArray(implode('.', $keys));
        }

        return $array;
    }

    /**
     * Write a language file from array.
     *
     * @param string $path
     * @param array  $translations
     *
     * @return false|int
     */
    protected function writeTranslationsToFile($translations, $path)
    {
        $content = "<?php \n\nreturn [";
        $content .= $this->stringLineMaker($translations);
        $content .= "\n];";

        return file_put_contents($path, $content);
    }

    /**
     * Sort strings array either by key or value.
     *
     * @param array $data
     * @param bool  $byKey
     */
    private function sortIfEnabled($data = [], $byKey = false): array
    {
        if (Config::get('translation-manager.sort-keys', true)) {
            return Arr::sort($data, function ($value, $key) use ($byKey) {
                return $byKey ? strtolower($key) : (is_array($value) ? $this->sortIfEnabled($value, $byKey) : strtolower($value));
            });
        }

        return $data;
    }

    /**
     * Get unique value recursively.
     *
     * @param array $array
     *
     * @return array
     */
    private function arrayUniqueRecursive($array)
    {
        $array = array_unique($array, SORT_REGULAR);

        foreach ($array as $key => $elem) {
            if (is_array($elem)) {
                $array[$key] = $this->arrayUniqueRecursive($elem);
            }
        }

        return $array;
    }

    /**
     * Write the lines of the inner array of the language file.
     *
     * @param $array
     * @param mixed $prepend
     *
     * @return string
     */
    private function stringLineMaker($array, $prepend = '')
    {
        $output = '';

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = $this->stringLineMaker($value, $prepend.'    ');
                $output .= "\n{$prepend}    '{$key}' => [{$value}\n{$prepend}    ],";
            } else {
                $value = str_replace('\"', '"', addslashes($value));
                $output .= "\n{$prepend}    '{$key}' => '{$value}',";
            }
        }

        return $output;
    }
}
