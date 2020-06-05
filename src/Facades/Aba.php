<?php

namespace ABWebDevelopers\AbaGenerator\Facades;

use Illuminate\Support\Facades\Facade;

class Aba extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'aba';
    }
}
