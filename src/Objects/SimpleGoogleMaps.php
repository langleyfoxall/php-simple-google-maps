<?php
namespace LangleyFoxall\SimpleGoogleMaps\Objects;

use LangleyFoxall\SimpleGoogleMaps\Objects\ApiAuthDrivers\BasicApiAuthDriver;
use LangleyFoxall\SimpleGoogleMaps\Objects\ApiAuthDrivers\EnterpriseApiAuthDriver;
use LangleyFoxall\SimpleGoogleMaps\Objects\CacheDrivers\DOFileCacheDriver;
use GuzzleHttp\Client;

/**
 * Class SimpleGoogleMaps
 * @package LangleyFoxall\SimpleGoogleMaps\Objects
 */
class SimpleGoogleMaps
{
    /**
     * @var EnterpriseApiAuthDriver
     */
    private $authObject;
    /**
     * @var string
     */
    private $baseUrl = "https://maps.googleapis.com/maps/api/";
    /**
     * @var bool
     */
    private $allowPartialMatches = false;
    /**
     * @var null
     */
    private $cache = null;

    /**
     * SimpleGoogleMaps constructor.
     * @param $key
     * @param $clientName
     * @param $cryptKey
     * @throws \Exception
     */
    public function __construct($key, $clientName, $cryptKey)
    {
        if (isset($key) && $key != null) {
            $this->authObject = new BasicApiAuthDriver($key);
        } else {
            $this->authObject = new EnterpriseApiAuthDriver($clientName, $cryptKey);
        }

        $this->cache = new DOFileCacheDriver;

    }

    /**
     * Allows partial matches for geocoding operations
     *
     * @param bool $allowPartial
     * @return SimpleGoogleMaps
     */
    public function allowPartialMatches($allowPartial = true)
    {
        $this->allowPartialMatches = $allowPartial;
        return $this;
    }

    /**
     * Look ups an address location, and returns a LatLong object containing its coordinates.
     *
     * @param string $address
     * @return LatLong|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function geocode(string $address)
    {
        $queryUrl = $this->authObject->applyToUrl(
            $this->baseUrl . "geocode/json?address=" . urlencode($address)
        );

        $cacheKey = sha1(serialize([__FUNCTION__, func_get_args()]));

        if (($results = $this->cache->get($cacheKey)) === false) {
            $response = (new Client())->request('GET', $queryUrl);
            $results = json_decode($response->getBody());
        }

        if (!$results) {
            throw new \Exception('Unable to parse response.');
        }

        if (!empty($results->error_message)) {
            throw new \Exception('Error from Google Maps API: '.$results->error_message);
        }

        if (!$results->results) {
            return null;
        }

        $result = $results->results[0];

        if (!$this->allowPartialMatches) {
            if (isset($result->partial_match) && $result->partial_match) {
                return null;
            }
        }

        $this->cache->set($cacheKey, $results);

        return new LatLong($result->geometry->location->lat, $result->geometry->location->lng);

    }

    /**
     * Look ups an LatLng location, and returns a string containing the address of that location.
     *
     * @param LatLong $latLong
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function reverseGeocode(LatLong $latLong)
    {
        $queryUrl = $this->authObject->applyToUrl(
            $this->baseUrl . "geocode/json?latlng=" . urlencode($latLong->lat.','.$latLong->long)
        );

        $cacheKey = sha1(serialize([__FUNCTION__, func_get_args()]));

        if (($results = $this->cache->get($cacheKey)) === false) {
            $response = (new Client())->request('GET', $queryUrl);
            $results = json_decode($response->getBody());
        }

        if (!$results) {
            throw new \Exception('Unable to parse response.');
        }

        if (!empty($results->error_message)) {
            throw new \Exception('Error from Google Maps API: '.$results->error_message);
        }

        if (!$results->results) {
            return null;
        }

        $result = $results->results[0];

        if (!isset($result->formatted_address)) {
            return null;
        }

        $this->cache->set($cacheKey, $results);

        return (string) $result->formatted_address;

    }
}
