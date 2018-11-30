<?php
namespace LangleyFoxall\SimpleGoogleMaps\Objects\CacheDrivers;

use DivineOmega\DOFileCache\DOFileCache;
use LangleyFoxall\SimpleGoogleMaps\Interfaces\CacheDriverInterface;

/**
 * Class DOFileCacheDriver
 * @package LangleyFoxall\SimpleGoogleMaps\Objects\CacheDrivers
 */
class DOFileCacheDriver implements CacheDriverInterface {

    /**
     * @var DOFileCache|null
     */
    private $cache = null;

    /**
     * DOFileCacheDriver constructor.
     */
    public function __construct()
    {
        $this->cache = new DOFileCache();
        $this->cache->changeConfig(
            array(
                "cacheDirectory" => __DIR__."/../../../cache/",
                "gzipCompression" => true
                )
            );
    }

    /**
     * @param $key
     * @param $value
     * @return bool
     */
    public function set($key, $value)
    {
        return $this->cache->set($key, $value, strtotime('+1 month'));
    }

    /**
     * @param $key
     * @return mixed
     * @throws \Exception
     */
    public function get($key)
    {
        return $this->cache->get($key);
    }

    /**
     * @param $key
     * @return bool
     */
    public function delete($key)
    {
        return $this->cache->delete($key);
    }
}