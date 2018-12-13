<?php
namespace LangleyFoxall\SimpleGoogleMaps\Factories;

use LangleyFoxall\SimpleGoogleMaps\Objects\SimpleGoogleMaps;

abstract class SimpleGoogleMapsFactory
{
    public static function getByKey($key)
    {
        return new SimpleGoogleMaps($key, null, null);
    }

    public static function getByClientNameAndCryptKey($clientName, $cryptKey)
    {
        return new SimpleGoogleMaps(null, $clientName, $cryptKey);
    }
}
