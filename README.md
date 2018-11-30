This library allows a user to easily communicate with the Google Maps API and retrieve a set of coordinates from an address.

## Installation
To install, just run the following composer command.

`composer require langleyfoxall/simple-google-maps-api`

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

## Geocoding

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

You can also calculate the distance between two `LatLong` objects by using the `distanceTo` method.

```php
$kilometresDistance = $homeCoords->distanceTo($toCoords);
```



