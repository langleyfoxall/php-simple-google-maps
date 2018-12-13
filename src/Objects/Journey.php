<?php
namespace LangleyFoxall\SimpleGoogleMaps\Objects;

use Illuminate\Support\Collection;

class Journey extends Collection
{
    public function from()
    {
        return $this->first()->from;
    }

    public function to()
    {
        return $this->last()->to;
    }

    public function distance()
    {
        $distance = 0;

        foreach ($this as $leg) {
            $distance += $leg->distance;
        }

        return $distance;
    }

    public function duration()
    {
        $duration = 0;

        foreach ($this as $leg) {
            $duration += $leg->duration;
        }

        return $duration;
    }
}
