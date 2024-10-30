<?php declare(strict_types=1);

namespace Mergado\Manager;

class DirectoryManager
{
    /*******************************************************************************************************************
     * CREATE DIR
     *******************************************************************************************************************/

    public static function createDir($dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    public static function checkAndCreateTmpDataDir(): void
    {
        if (is_dir(__MERGADO_MMP_UPLOAD_DIR__)) {
            mkdir(__MERGADO_MMP_UPLOAD_DIR__);
        }

        if(!is_dir(__MERGADO_TMP_DIR__)) {
            mkdir(__MERGADO_TMP_DIR__);
        }

        if(!is_dir(__MERGADO_XML_DIR__)) {
            mkdir(__MERGADO_XML_DIR__);
        }
    }
}
