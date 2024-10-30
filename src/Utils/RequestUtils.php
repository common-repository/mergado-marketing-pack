<?php

namespace Mergado\Utils;

use Exception;

class RequestUtils
{
    public static function fileGetContents($url, $timeout = 5, $connectionTimeout = 5)
    {
        if (extension_loaded('curl')) {
            try {
                $c = curl_init();
                curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($c, CURLOPT_URL, $url);
                curl_setopt($c, CURLOPT_TIMEOUT, $timeout);
                curl_setopt($c, CURLOPT_CONNECTTIMEOUT, $connectionTimeout);

                $contents = curl_exec($c);

                curl_close($c);

                if ($contents) {
                    return $contents;
                }

                return false;
            } catch (Exception $e) {
                return false;
            }
        } else {
            return file_get_contents($url);
        }
    }
}
