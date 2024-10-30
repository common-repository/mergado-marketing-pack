<?php

namespace Mergado\Service\Cookie\Plugins;

class CookieYes
{
    public const COOKIE_DATA = 'cookieyes-consent';
    public const COOKIE_DATA_SEPARATOR = ',';

    public const SUB_COOKIE_ADVERTISEMENT = 'advertisement';
    public const SUB_COOKIE_ANALYTICAL = 'analytics';
    public const SUB_COOKIE_FUNCTIONAL = 'functional';

    private $parsedCookieYesDataCache;

    public function isFunctionalActive(): bool {
        $data = $this->getCookieYesData();

        // If cookie exist and key isset
        if ($data && isset($data[self::SUB_COOKIE_FUNCTIONAL])) {
            return $data[self::SUB_COOKIE_FUNCTIONAL] === 'yes';
        }

        return false;
    }

    public function isAnalyticalActive(): bool {
        $data = $this->getCookieYesData();

        // If cookie exist and key isset
        if ($data && isset($data[self::SUB_COOKIE_ANALYTICAL])) {
            return $data[self::SUB_COOKIE_ANALYTICAL] === 'yes';
        }

        return false;
    }

    public function isAdvertisementActive(): bool {
        $data = $this->getCookieYesData();

        // If cookie exist and key isset
        if ($data && isset($data[self::SUB_COOKIE_ADVERTISEMENT])) {
            return $data[self::SUB_COOKIE_ADVERTISEMENT] === 'yes';
        }

        return false;
    }

    public function getCookieYesData()
    {
        // Return cached value
        if ($this->parsedCookieYesDataCache !== null) {
            return $this->parsedCookieYesDataCache;
        }

        // Take value from cookie
        if ($_COOKIE && isset($_COOKIE[self::COOKIE_DATA])) {
            // Split the string by commas
            $parts = explode(self::COOKIE_DATA_SEPARATOR, $_COOKIE[self::COOKIE_DATA]);

            // Initialize an empty array to store the key-value pairs
            $data = [];

            // Iterate through the parts and split each part by colon to create key-value pairs
            foreach ($parts as $part) {
                $pair = explode(":", $part);
                if (count($pair) === 2) {
                    $key = trim($pair[0]);
                    $value = trim($pair[1]);
                    $data[$key] = $value;
                }
            }

            $this->parsedCookieYesDataCache = $data;
        } else {
            $this->parsedCookieYesDataCache = false;
        }

        return $this->parsedCookieYesDataCache;
    }
}
