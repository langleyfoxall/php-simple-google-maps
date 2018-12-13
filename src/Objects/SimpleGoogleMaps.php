<?php
namespace LangleyFoxall\SimpleGoogleMaps\Objects;

use LangleyFoxall\SimpleGoogleMaps\Objects\ApiAuthDrivers\BasicApiAuthDriver;
use LangleyFoxall\SimpleGoogleMaps\Objects\ApiAuthDrivers\EnterpriseApiAuthDriver;
use LangleyFoxall\SimpleGoogleMaps\Objects\CacheDrivers\DOFileCacheDriver;
use GuzzleHttp\Client;
use LangleyFoxall\SimpleGoogleMaps\Objects\Enums\TravelMode;

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

    /**
     * Retrieves directions between two points ($from, and $to), using the travel mode
     * defined by the $travelMode (TravelMode enum).
     *
     * @param LatLong|string $from
     * @param LatLong|string $to
     * @param string $travelMode
     * @return Journey|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function directions($from, $to, $travelMode = TravelMode::DRIVING) {

        if (is_object($from) && get_class($from) === LatLong::class) {
            $from = $from->lat.','.$from->long;
        }

        if (is_object($to) && get_class($to) === LatLong::class) {
            $to = $to->lat.','.$to->long;
        }

        $queryUrl = $this->authObject->applyToUrl(
            $this->baseUrl.'directions/json?origin='.urlencode($from).
                '&destination='.urlencode($to).'&mode='.$travelMode
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

        if (!$results->routes) {
            return null;
        }

        $this->cache->set($cacheKey, $results);

        $route = $results->routes[0];

        $journey = new Journey();

        foreach($route->legs as $routeLeg) {
            foreach($routeLeg->steps as $step) {

                $description = html_entity_decode(str_replace('  ', ' ',
                    preg_replace('#<[^>]+>#', ' ', $step->html_instructions)
                ));

                $journey->push(new JourneyStep(
                    new LatLong(
                        $step->start_location->lat,
                        $step->start_location->lng
                    ),
                    new LatLong(
                        $step->end_location->lat,
                        $step->end_location->lng
                    ),
                    $step->distance->value,
                    $step->duration->value,
                    $description
                ));
            }
        }

        return $journey;
    }
}
