# PHP Simple Google Maps

[![StyleCI](https://github.styleci.io/repos/159809368/shield?branch=master)](https://github.styleci.io/repos/159809368)

This package provides a simple PHP client for various Google Maps APIs.

## Installation
To install, just run the following composer command.

```bash
composer require langleyfoxall/simple-google-maps
```

Remember to include the `vendor/autoload.php` file if your framework does not do this for you.

## Usage

To use Simple Google Maps, you must first create a new instance. This can be done is two ways,
dependant on whether you have a standard API `key`, or a `clientName` and `cryptKey` (for enterprise
/ premium plans).

```php
// Standard authentication:
$simpleGoogleMaps = SimpleGoogleMapsFactory::getByKey(getenv('KEY'));

// Enterprise / premium plan authentication:
$simpleGoogleMaps = SimpleGoogleMapsFactory::getByClientNameAndCryptKey(getenv('CLIENT_NAME'), getenv('CRYPT_KEY'));
```

### Geocoding

To convert an address to a set of GPS coordinates, use the `geocode` method, as shown below.

```php
$latLng = $simpleGoogleMaps->geocode('10 Downing St, Westminster, London SW1A UK');
```

Optionally, you can allow partial matches to be returned if your input address is not highly accurate. 
You can do so with the `allowPartialMatches` method, as shown below.

```php
$latLng = $simpleGoogleMaps->allowPartialMatches()->geocode('test address');
```

The above method will return a object of type `LatLong`, which allows you to access the GPS coordinates as
shown below.

```php
$latitude = $latLng->lat;
$longitude = $latLng->long;
``` 

You can also calculate the distance between two `LatLong` objects by using the `distanceTo` method. The
distance is returned in kilometers, and takes into account the curvature of the Earth using the Haversine
formula.

```php
$distance = $fromCoords->distanceTo($toCoords);
```

### Reverse Geocoding

To lookup an address from a set of GPS coordinate, use the `reverseGeocode` method, as shown below.

```php
$address = $simpleGoogleMaps->reverseGeocode(new LatLong(51.5033635, -0.1276248));
```

This method will return a string containing the address found at the specified coordinates. If no address
could be found, `null` will be returned.

### Directions

To find the directions between two points, use the `directions` method. The methods expects
three parameters, the origin, the destination, and optionally the travel mode as defined by the
`TravelMode` enum.

See the example usage below.

```php
$address1 = "10 Downing St, Westminster, London SW1A UK";
$address2 = "Schott House, Drummond Rd, Stafford ST16 3EL";

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
```

This will produce output similar to the following.

```
134 secs        452 m           Head north on Whitehall / A3212 toward Horse Guards Ave May be closed at certain times or days 
203 secs        1029 m          At the roundabout, take the 1st exit onto The Mall Parts of this road may be closed at certain times or days 
121 secs        688 m           Turn right onto Constitution Hill 
34 secs         141 m           Turn left onto Duke of Wellington Pl Leaving toll zone 
20 secs         83 m            Turn right onto Grosvenor Pl 
21 secs         107 m           Slight right onto Piccadilly May be closed at certain times or days 
164 secs        1244 m          Slight left onto Park Ln / A4202 
35 secs         199 m           Slight left onto Cumberland Gate 
16 secs         68 m            Turn right onto Bayswater Rd 
92 secs         410 m           Slight left onto Edgware Rd / A5 Entering toll zone in 280 m at Upper Berkeley St Leaving toll zone in 300 m at Stourcliffe St 
48 secs         177 m           Turn right onto George St Entering toll zone 
152 secs        531 m           Turn left onto Seymour Pl 
46 secs         231 m           Turn left onto Marylebone Rd / A501 Leaving toll zone 
722 secs        9209 m          Keep right to continue on Marylebone Flyover / A40 Continue to follow A40 
708 secs        14581 m         Keep right to continue on Western Ave / A40 
4636 secs       140047 m        Keep right to continue on M40 , follow signs for M25 / Birmingham / Oxford / Beaconsfield 
49 secs         1164 m          At junction 3A , take the M42 / Railway Station / Airport exit to M1 / M6 / Birmingham (E, N & C) / Solihull / N.E.C. 
856 secs        22091 m         Merge onto M42 
14 secs         375 m           Keep right at the fork to continue on M6 Toll 
1121 secs       33363 m         Keep right at the fork to stay on M6 Toll Toll road 
756 secs        19905 m         Continue onto M6 
23 secs         206 m           At junction 14 , take the A34 exit to Stone / Stafford (N) 
103 secs        1213 m          At the roundabout, take the 3rd exit onto A34 Go through 1 roundabout 
80 secs         925 m           At the roundabout, take the 2nd exit onto Beaconside / A513 
128 secs        1550 m          Turn right onto Common Rd 
13 secs         123 m           Turn left onto Astonfields Rd 
76 secs         395 m           Turn left onto Drummond Rd Destination will be on the left 
Totals: 10371 secs, 250507 km
```

