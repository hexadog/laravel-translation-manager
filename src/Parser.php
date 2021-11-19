<?php

namespace Hexadog\TranslationManager;

use Hexadog\TranslationManager\Contracts\Parser as ContractsParser;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Finder\SplFileInfo;

class Parser implements ContractsParser
{
    /**
     * Translation function names.
     *
     * @var array
     */
    protected $functions;

    /**
     * Translation function pattern.
     *
     * @var string
     */
    protected $pattern = '/([FUNCTIONS])\([\'"](.+)[\'"][\),]/U';

    /**
     * Parser constructor.
     */
    public function __construct()
    {
        $this->functions = Config::get('translation-manager.functions', ['__', '_t', '@lang']);
        $this->pattern = str_replace('[FUNCTIONS]', implode('|', $this->functions), $this->pattern);
    }

    /**
     * Parse a file in order to find translatable strings.
     */
    public function parse(SplFileInfo $file): array
    {
        $strings = [];

        $data = $file->getContents();

        if (!preg_match_all($this->pattern, $data, $matches, PREG_OFFSET_CAPTURE)) {
            // If pattern not found return
            return $strings;
        }

        foreach (current($matches) as $match) {
            preg_match($this->pattern, $match[0], $string);

            $strings[] = $string[2];
        }

        // Remove duplicates.
        return array_unique($strings);
    }
}
