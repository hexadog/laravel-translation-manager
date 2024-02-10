<?php

declare(strict_types=1);

namespace Hexadog\TranslationManager\Contracts;

use Symfony\Component\Finder\SplFileInfo;

interface Parser
{
    public function parse(SplFileInfo $file): array;
}
