<?php declare(strict_types=1);

namespace Mergado\Helper;

class FieldSanitizationHelper
{
    public const FIELD_TYPE_TEXT = 'field_type_text';
    public const FIELD_TYPE_TOGGLE = 'field_type_toggle';

    public static function sanitizeText($value): string
    {
        return sanitize_text_field($value);
    }

    public static function sanitizeToggle($value): int
    {
        if ($value === 'on') {
            return 1;
        }

        return 0;
    }

    public static function sanitizeByType($value, $type)
    {
        switch ($type) {
            case self::FIELD_TYPE_TEXT:
                return self::sanitizeText($value);
            case self::FIELD_TYPE_TOGGLE:
                return self::sanitizeToggle($value);
        }

        // Default, value not sanitized
        return $value;
    }

    public static function normalizeInputs($values, $defaultFilter) : array
    {
        $data = [];

        foreach ($values as $key => $value) {
            if (is_numeric($key)) {
                $data[$value] = $defaultFilter;
            } else {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
