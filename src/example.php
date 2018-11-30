<?php
require_once __DIR__.'/../vendor/autoload.php';

use LangleyFoxall\SimpleGoogleMaps\Factories\SimpleGoogleMapsFactory;

$address1 = "10 Downing St, Westminster, London SW1A UK";
$address2 = "Schott House, Drummond Rd, Stafford ST16 3EL";

// Standard authentication:
$simpleGoogleMaps = SimpleGoogleMapsFactory::getByKey(getenv('KEY'));

// Enterprise / premium plan authentication:
// $simpleGoogleMaps = SimpleGoogleMapsFactory::getByClientNameAndCryptKey(getenv('CLIENT_NAME'), getenv('CRYPT_KEY'));

$fromCoords = $simpleGoogleMaps->geocode($address1);
$toCoords = $simpleGoogleMaps->geocode($address2);

var_dump($fromCoords, $toCoords);

$distance = $fromCoords->distanceTo($toCoords);

var_dump($distance);


