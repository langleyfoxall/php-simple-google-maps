<?php
require_once __DIR__.'/../vendor/autoload.php';

use LangleyFoxall\SimpleGoogleMaps\Factories\SimpleGoogleMapsFactory;
use LangleyFoxall\SimpleGoogleMaps\Objects\Enums\TravelMode;
use LangleyFoxall\SimpleGoogleMaps\Objects\LatLong;

$address1 = "10 Downing St, Westminster, London SW1A UK";
$address2 = "Schott House, Drummond Rd, Stafford ST16 3EL";

// Standard authentication:
$simpleGoogleMaps = SimpleGoogleMapsFactory::getByKey(getenv('KEY'));

// Enterprise / premium plan authentication:
// $simpleGoogleMaps = SimpleGoogleMapsFactory::getByClientNameAndCryptKey(getenv('CLIENT_NAME'), getenv('CRYPT_KEY'));

echo 'Geocoding:'.PHP_EOL;

$fromCoords = $simpleGoogleMaps->geocode($address1);
$toCoords = $simpleGoogleMaps->geocode($address2);

var_dump($fromCoords, $toCoords);

echo 'Distance calculation:'.PHP_EOL;

$distance = $fromCoords->distanceTo($toCoords);

var_dump($distance);

echo 'Reverse geocoding:'.PHP_EOL;

$reverseGeocodeAddress1 = $simpleGoogleMaps->reverseGeocode(new LatLong(51.5033635, -0.1276248));
$reverseGeocodeAddress2 = $simpleGoogleMaps->reverseGeocode(new LatLong(52.8220531, -2.1127185));

var_dump($reverseGeocodeAddress1, $reverseGeocodeAddress2);

echo 'Directions:'.PHP_EOL;

$journey = $simpleGoogleMaps->directions($address1, $address2, TravelMode::DRIVING);

foreach($journey as $step) {
    echo $step->duration.' secs  ';
    echo "\t";
    echo $step->distance.' m    ';
    echo "\t";
    echo $step->description;
    echo PHP_EOL;
}

echo 'Totals: '.$journey->duration().' secs, '.$journey->distance().' km';
echo PHP_EOL;
