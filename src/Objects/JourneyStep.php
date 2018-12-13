<?php

namespace LangleyFoxall\SimpleGoogleMaps\Objects;

class JourneyStep
{
    public $from;
    public $to;
    public $distance;
    public $duration;
    public $description;

    public function __construct(LatLong $from, LatLong $to, $distance, $duration, $description = '')
    {
        $this->from = $from;
        $this->to = $to;
        $this->distance = $distance;
        $this->duration = $duration;
        $this->description = $description;
    }
}
