<?php declare(strict_types=1);

namespace Mergado\Helper;

class ProductHelper
{
    public static function getMergedIdFromCartItem($item)
    {
        if ($item['variation_id'] == 0) {
            return $item['product_id'];
        }

        return $item['product_id'] . '-' . $item['variation_id'];
    }
}
