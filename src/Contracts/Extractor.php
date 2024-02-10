<?php

declare(strict_types=1);

namespace Hexadog\TranslationManager\Contracts;

use Illuminate\Support\Collection;

interface Extractor
{
    public function __construct(Finder $finder, Parser $parser);

    public function extract(): Collection;
}
