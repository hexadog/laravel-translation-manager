<?php

declare(strict_types=1);

namespace Hexadog\TranslationManager;

use Hexadog\TranslationManager\Contracts\Extractor as ContractsExtractor;
use Hexadog\TranslationManager\Contracts\Finder;
use Hexadog\TranslationManager\Contracts\Parser;
use Illuminate\Support\Collection;

class Extractor implements ContractsExtractor
{
    /**
     * Extractor Finder.
     *
     * @var Finder
     */
    protected $finder;

    /**
     * Extractor parser.
     *
     * @var Parser
     */
    protected $parser;

    public function __construct(Finder $finder, Parser $parser)
    {
        $this->finder = $finder;
        $this->parser = $parser;
    }

    /**
     * Extract translatable strings from the files.
     */
    public function extract(): Collection
    {
        $strings = [];

        // List files
        $files = $this->finder->find();

        // Get all translatable strings from files
        foreach ($files as $file) {
            $strings = array_merge($strings, $this->parser->parse($file));
        }

        return collect($strings);
    }
}
