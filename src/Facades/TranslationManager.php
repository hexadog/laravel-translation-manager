<?php

namespace Hexadog\TranslationManager\Facades;

use Illuminate\Support\Facades\Facade;
use Hexadog\TranslationManager\TranslationManager as Manager;

class TranslationManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Manager::class;
    }
}
