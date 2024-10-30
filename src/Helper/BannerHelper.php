<?php

namespace Mergado\Helper;

use Mergado\Utils\RequestUtils;

class BannerHelper
{
    public static function getWide()
    {
        return RequestUtils::fileGetContents('https://platforms.mergado.com/woocommerce/wide');
    }

    public static function getSidebar()
    {
        return RequestUtils::fileGetContents('https://platforms.mergado.com/woocommerce/sidebar');
    }
}
