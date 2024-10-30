<?php declare(strict_types=1);

namespace Mergado\Utils;

class TypeConverter
{
    public static function boolToActive($bool): string
    {
        return $bool ? 'active' : 'inactive';
    }
}
