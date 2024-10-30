<?php declare(strict_types=1);

namespace Mergado\Service\External\Argep;

use Mergado;
use Mergado\Manager\DatabaseManager;
use Mergado\Traits\SingletonTrait;

class ArgepService
{
    use SingletonTrait;

    public const CONVERSION_ACTIVE = 'mmp-agrep-conversion-active';
    public const CONVERSION_CODE = 'mmp-agrep-conversion-code';
    public const CONVERSION_LABEL = 'mmp-agrep-conversion-label';

    /******************************************************************************************************************
     * IS
     ******************************************************************************************************************/

    public function isConversionActive(): bool
    {
        $active = $this->getConversionActive();
        $code = $this->getConversionCode();
        $label = $this->getConversionLabel();

        return $active === 1 && $code && $code !== '' && $label && $label !== '';
    }

    /*******************************************************************************************************************
     * Get field value
     *******************************************************************************************************************/

    public function getConversionActive(): int
    {
        return (int)get_option(self::CONVERSION_ACTIVE, 0);
    }

    public function getConversionCode(): string
    {
        return get_option(self::CONVERSION_CODE, '');
    }

    public function getConversionLabel(): string
    {
        return get_option(self::CONVERSION_LABEL, '');
    }


    /*******************************************************************************************************************
     * SAVE FIELDS
     ******************************************************************************************************************/

    public static function saveFields(array $post): void
    {
        DatabaseManager::saveOptions($post, [
            self::CONVERSION_ACTIVE
        ], [
            self::CONVERSION_CODE,
            self::CONVERSION_LABEL
        ]);
    }
}
