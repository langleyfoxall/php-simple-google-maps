<?php

namespace LangleyFoxall\SimpleGoogleMaps\Objects;

use DivineOmega\Distance\Distance;
use DivineOmega\Distance\Point;
use DivineOmega\Distance\Types\Haversine;

/**
 * Class LatLong
 * @package LangleyFoxall\SimpleGoogleMaps\Objects
 */
class LatLong
{
    /**
     * @var
     */
    public $lat;
    /**
     * @var
     */
    public $long;

    /**
     * LatLong constructor.
     * @param $lat
     * @param $long
     */
    public function __construct($lat, $long)
    {
        $this->lat = $lat;
        $this->long = $long;
    }

    /**
     * Given another LatLong object, calculates and returns the distance between the two
     * locations in kilometeres, taking into account the curvature of the Earth using the
     * Haversine formula.
     *
     * @param LatLong $destination
     * @return mixed
     */
    public function distanceTo(LatLong $destination)
    {
        $earthRadius = 6371;

        return (new Distance())
            ->type(new Haversine($earthRadius))
            ->from($this->getPoint())
            ->to($destination->getPoint())
            ->get();
    }

    /**
     * Returns a PHP Distance Point object for this LatLng object.
     *
     * @return Point
     */
    public function getPoint() {
        return new Point($this->lat, $this->long);
    }
}