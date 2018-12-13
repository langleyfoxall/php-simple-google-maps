<?php
namespace LangleyFoxall\SimpleGoogleMaps\Objects\ApiAuthDrivers;

use Exception;
use LangleyFoxall\SimpleGoogleMaps\Interfaces\ApiAuthInterface;

/**
 * Class EnterpriseApiAuthDriver.
 */
class EnterpriseApiAuthDriver implements ApiAuthInterface
{
    /**
     * @var string API client name
     */
    private $clientName;
    /**
     * @var string API crypt key
     */
    private $cryptKey;

    /**
     * EnterpriseApiAuthDriver constructor.
     *
     * @param $clientName
     * @param $cryptKey
     *
     * @throws Exception
     */
    public function __construct($clientName, $cryptKey)
    {
        if (!$clientName) {
            throw new Exception('ClientName not set');
        }

        if (!$cryptKey) {
            throw new Exception('CryptKey not set');
        }
        $this->clientName = $clientName;
        $this->cryptKey = $cryptKey;
    }

    /**
     * @param $url
     *
     * @return string
     */
    public function applyToUrl($url)
    {
        $urlWithClient = $url.'&client='.$this->clientName;

        $parsedUrl = parse_url($urlWithClient);

        $urlToEncode = $parsedUrl['path'].'?'.$parsedUrl['query'];
        $baseKey = base64_decode(str_replace(['-', '_'], ['+', '/'], $this->cryptKey));

        $signature = hash_hmac('sha1', $urlToEncode, $baseKey, true);

        $encodedSignature = str_replace(['+', '/'], ['-', '_'], base64_encode($signature));

        $appendedUrl = $urlWithClient.'&signature='.$encodedSignature;

        return $appendedUrl;
    }
}
