<?php

declare(strict_types=1);

namespace Hexadog\TranslationManager\Facades;

use Hexadog\TranslationManager\TranslationManager as Manager;
use Illuminate\Support\Facades\Facade;

class TranslationManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Manager::class;
    }
}
