<?php

namespace Iodev\Whois;

class Config
{
    /** @var array  */
    private static $cachedData = [];

    /**
     * @param string $name
     * @return array|mixed
     */
    public static function load($name)
    {
        if (!isset(self::$cachedData[$name])) {
            self::$cachedData[$name] = self::loadJson($name);
        }
        return self::$cachedData[$name];
    }

    /**
     * @param string $name
     * @return array|mixed
     */
    private static function loadJson($name)
    {
        $json = file_get_contents(__DIR__."/Configs/$name.json");
        return json_decode($json, true);
    }
}
