<?php

namespace Mergado\Service;

use Mergado\Manager\DatabaseManager;
use Mergado\Service\Cookie\Plugins\Complianz;
use Mergado\Service\Cookie\Plugins\CookieBot;
use Mergado\Service\Cookie\Plugins\CookieYes;
use Mergado\Service\Cookie\Plugins\CookieYesOld;
use Mergado\Traits\SingletonTrait;

class CookieService {

    use SingletonTrait;

	// Cookie form
	public const FIELD_COOKIES_ENABLE = 'form-cookie-enable-always';
	public const FIELD_ADVERTISEMENT_USER = 'form-cookie-advertisement';
	public const FIELD_ANALYTICAL_USER = 'form-cookie-analytical';
	public const FIELD_FUNCTIONAL_USER = 'form-cookie-functional';

    private $analyticalEnabledCache;
    private $advertisementEnabledCache;
    private $functionalEnabledCache;

    /**
     * @var CookieBot
     */
    private $cookieBot;

    /**
     * @var CookieYes
     */
    private $cookieYes;

    /**
     * @var CookieYesOld
     */
    private $cookieYesOld;

    /**
     * @var Complianz
     */
    private $complianz;

    public function __construct()
    {
        $this->cookieYes = new CookieYes();
        $this->cookieYesOld = new CookieYesOld();
        $this->complianz = new Complianz();
        $this->cookieBot = new CookieBot();
    }

    /**
     * Google Analytics (gtag.js)
     */
    public function analyticalEnabled() : bool {
        // Return cache if available
        if ($this->analyticalEnabledCache !== null) {
            return $this->analyticalEnabledCache;
        }

        // Cookies not enabled in admin
        if ( !$this->isCookieBlockingEnabled() ) {
            $this->analyticalEnabledCache = true;
            return $this->analyticalEnabledCache;
        }

        // Supported plugins cookies
        if ($this->complianz->isAnalyticalActive() ||
            $this->cookieYesOld->isAnalyticalActive() ||
            $this->cookieYes->isAnalyticalActive() ||
            $this->cookieBot->isAnalyticalActive()
        ) {
            $this->analyticalEnabledCache = true;
            return $this->analyticalEnabledCache;
        }

        // Custom cookie name from admin
        $cookieName = $this->getAnalyticalCustomName();

        if ($cookieName !== '' && $this->isCookieActive($cookieName)) {
            $this->analyticalEnabledCache = true;
        } else {
            $this->analyticalEnabledCache = false;
        }

        return $this->analyticalEnabledCache;
    }

    /**
     * Glami Pixel, Biano Pixel, etarget, Sklik, Kelkoo, Heureka order confirmation
     */
    public function advertisementEnabled() : bool {
        // Return cached value if available
        if ($this->advertisementEnabledCache !== null) {
            return $this->advertisementEnabledCache;
        }

        // Cookies not enabled in admin
        if ( !$this->isCookieBlockingEnabled() ) {
            $this->advertisementEnabledCache = true;
            return $this->advertisementEnabledCache;
        }

        // Supported plugins cookies
        if ( $this->complianz->isAdvertisementActive() ||
            $this->cookieYesOld->isAdvertisementActive() ||
            $this->cookieYes->isAdvertisementActive() ||
            $this->cookieBot->isAdvertisementActive()
        ) {
            $this->advertisementEnabledCache = true;
            return $this->advertisementEnabledCache;
        }

        // Custom cookie name from admin
        $cookieName = $this->getAdvertisementCustomName();

        if ($cookieName !== '' && $this->isCookieActive($cookieName)) {
            $this->analyticalEnabledCache = true;
        } else {
            $this->analyticalEnabledCache = false;
        }

        return $this->analyticalEnabledCache;
    }

    /**
     * Heureka widget
     */
    public function functionalEnabled(): bool
    {
        // Return cached value if available
        if ($this->functionalEnabledCache !== null) {
            return $this->functionalEnabledCache;
        }

        // Cookies not enabled in admin
        if ( !$this->isCookieBlockingEnabled() ) {
            $this->functionalEnabledCache = true;
            return $this->functionalEnabledCache;
        }

        // Supported plugins cookies
        if ( $this->complianz->isFunctionalActive() ||
            $this->cookieYesOld->isFunctionalActive() ||
            $this->cookieYes->isFunctionalActive() ||
            $this->cookieBot->isFunctionalActive()
        ) {
            $this->functionalEnabledCache = true;
            return $this->functionalEnabledCache;
        }

        // Custom cookie name from admin
        $cookieName = $this->getFunctionalCustomName();

        if ($cookieName !== '' && $this->isCookieActive($cookieName)) {
            $this->functionalEnabledCache = true;
        } else {
            $this->functionalEnabledCache = false;
        }

        return $this->functionalEnabledCache;
    }

	// HELPER

    public static function isCookieActive( string $cookieName ): bool
    {
        return isset($_COOKIE[$cookieName]) && self::isActiveStatus($_COOKIE[$cookieName]);
    }

    public static function isActiveStatus($cookieValue): bool
    {
        return filter_var($cookieValue, FILTER_VALIDATE_BOOLEAN) || $cookieValue === 'allow';
    }

	// ADMIN FORM VALUES
	public function isCookieBlockingEnabled(): bool
    {
		$val = get_option( self::FIELD_COOKIES_ENABLE );

		if ( trim( $val ) !== '' ) {
			if ( filter_var( $val, FILTER_VALIDATE_BOOLEAN ) ) {
				return true;
			}

            return false;
        }

        return false;
    }

	public function getAdvertisementCustomName() {
		$val = get_option( self::FIELD_ADVERTISEMENT_USER );

		if ( trim( $val ) !== '' ) {
			return $val;
		}

        return '';
    }

	public function getAnalyticalCustomName() {
		$val = get_option( self::FIELD_ANALYTICAL_USER );

		if ( trim( $val ) !== '' ) {
			return $val;
		}

        return '';
    }

	public function getFunctionalCustomName() {
		$val = get_option( self::FIELD_FUNCTIONAL_USER );

		if ( trim($val) !== '' ) {
			return $val;
		}

        return '';
    }



    /*******************************************************************************************************************
     * Javascript
     ******************************************************************************************************************/

    public function createJsVariables(): void
    {
        $this->jsAddCustomerVariableNames();
    }

    public function jsAddCustomerVariableNames(): void
    {
        // Array filter removes empty string if custom name is ''
        $analyticalNames = implode('", "',array_filter([CookieYesOld::COOKIE_ANALYTICAL, Complianz::COOKIE_ANALYTICAL, $this->getAnalyticalCustomName()]));
        $advertisementNames = implode('", "',array_filter([CookieYesOld::COOKIE_ADVERTISEMENT, Complianz::COOKIE_ADVERTISEMENT, $this->getAdvertisementCustomName()]));
        $functionalNames = implode('", "',array_filter([CookieYesOld::COOKIE_FUNCTIONAL, Complianz::COOKIE_FUNCTIONAL, $this->getFunctionalCustomName()]));

        ?>
            <script>
               window.mmp.cookies = {
                  functions: {},
                  sections: {
                    functional: {
                      onloadStatus: <?php echo (int) $this->functionalEnabled() ?>,
                      functions: {},
                      names: {
                        simple: [],
                        arrays: []
                      },
                    },
                    analytical: {
                      onloadStatus: <?php echo (int) $this->analyticalEnabled() ?>,
                      functions: {},
                      names: {
                        simple: [],
                        arrays: []
                      }
                    },
                    advertisement: {
                      onloadStatus: <?php echo (int) $this->advertisementEnabled() ?>,
                      functions: {},
                      names: {
                        simple: [],
                        arrays: []
                      }
                    }
                 }
               };

               // Simple
               window.mmp.cookies.sections.functional.names.simple = ["<?php echo $functionalNames ?>"];
               window.mmp.cookies.sections.advertisement.names.simple = ["<?php echo $advertisementNames ?>"];
               window.mmp.cookies.sections.analytical.names.simple = ["<?php echo $analyticalNames ?>"];

               // Arrays
               window.mmp.cookies.sections.functional.names.arrays =  [{
                 name: '<?php echo CookieYes::COOKIE_DATA ?>',
                 key: '<?php echo CookieYes::SUB_COOKIE_FUNCTIONAL ?>',
                 getConsentDataFunction: 'getCookieYesConsent',
               },
               {
                 name: '<?php echo CookieBot::COOKIE_DATA ?>',
                 key: '<?php echo CookieBot::SUB_COOKIE_FUNCTIONAL ?>',
                 getConsentDataFunction: 'getCookieBotConsent'
               }];

               window.mmp.cookies.sections.advertisement.names.arrays = [{
                 name: '<?php echo CookieYes::COOKIE_DATA ?>',
                 key: '<?php echo CookieYes::SUB_COOKIE_ADVERTISEMENT ?>',
                 getConsentDataFunction: 'getCookieYesConsent',
               },
               {
                 name: '<?php echo CookieBot::COOKIE_DATA ?>',
                 key: '<?php echo CookieBot::SUB_COOKIE_ADVERTISEMENT ?>',
                 getConsentDataFunction: 'getCookieBotConsent'
               }
               ];

               window.mmp.cookies.sections.analytical.names.arrays = [{
                 name: '<?php echo CookieYes::COOKIE_DATA ?>',
                 key: '<?php echo CookieYes::SUB_COOKIE_ANALYTICAL ?>',
                 getConsentDataFunction: 'getCookieYesConsent',
               },
                 {
                   name: '<?php echo CookieYes::COOKIE_DATA ?>',
                   key: '<?php echo CookieBot::SUB_COOKIE_ANALYTICAL ?>',
                   getConsentDataFunction: 'getCookieBotConsent'
                 }
               ];
            </script>
        <?php
    }

	/*******************************************************************************************************************
	 * SAVE FIELDS
	 ******************************************************************************************************************/

	public static function saveFields(array $post): void {
		DatabaseManager::saveOptions($post, [
			self::FIELD_COOKIES_ENABLE,
		], [
			self::FIELD_ANALYTICAL_USER,
			self::FIELD_ADVERTISEMENT_USER,
			self::FIELD_FUNCTIONAL_USER
		]);
	}
}
