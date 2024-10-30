<?php declare(strict_types=1);

namespace Mergado\Utils;

class FileUtils
{
    public static function getLastModified($filePath)
    {
        if (file_exists($filePath)) {
            $lastUpdate = filemtime($filePath);

            if ($lastUpdate) {
                return $lastUpdate;
            }
        }

        return false;
    }
}
