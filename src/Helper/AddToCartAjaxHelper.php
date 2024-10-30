<?php declare(strict_types=1);

namespace Mergado\Helper;

class AddToCartAjaxHelper
{
    public static function getDisabledActionNames() {
        return [
            'xoo_wsc_add_to_cart',
            'woodmart_ajax_add_to_cart',
            'reycore_ajax_add_to_cart'
        ];
    }
}
