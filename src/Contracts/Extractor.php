<?php

namespace Hexadog\TranslationManager\Contracts;

use Hexadog\TranslationManager\Contracts\Finder;
use Hexadog\TranslationManager\Contracts\Parser;
use Illuminate\Support\Collection;

interface Extractor
{
    public function __construct(Finder $finder, Parser $parser);

    public function extract(): Collection;
}
