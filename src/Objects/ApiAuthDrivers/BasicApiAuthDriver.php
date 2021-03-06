<?php
namespace LangleyFoxall\SimpleGoogleMaps\Objects\ApiAuthDrivers;

use Exception;
use LangleyFoxall\SimpleGoogleMaps\Interfaces\ApiAuthInterface;

/**
 * Class BasicApiAuthDriver.
 */
class BasicApiAuthDriver implements ApiAuthInterface
{
    /**
     * @var string API Key
     */
    private $key;

    /**
     * BasicApiAuthDriver constructor.
     *
     * @param $key
     *
     * @throws Exception
     */
    public function __construct($key)
    {
        if (!$key) {
            throw new Exception('No key set');
        }
        $this->key = $key;
    }

    /**
     * @param $url
     *
     * @return string
     */
    public function applyToUrl($url)
    {
        $authString = '&key='.$this->key;
        $appendedUrl = $url.$authString;

        return $appendedUrl;
    }
}
