<?php

namespace Mergado\Service;

use Mergado\Feed\Category\CategoryFeed;
use Mergado\Feed\Customer\CustomerFeed;
use Mergado\Feed\Product\ProductFeed;
use Mergado\Feed\Stock\StockFeed;
use Mergado\Traits\SingletonTrait;

class AlertService {

    use SingletonTrait;
	public const FEED_TO_SECTION = [
		'product' => ProductFeed::FEED_SECTION,
		'category' => CategoryFeed::FEED_SECTION,
		'customer' => CustomerFeed::FEED_SECTION,
		'stock' => StockFeed::FEED_SECTION
	];

	//SINGLE ALERT NAMES .. in prestashop function that add blogId
	public const ALERT_NAMES = [
		'NO_FEED_UPDATE' => 'feed_not_updated',
		'ERROR_DURING_GENERATION' => 'generation_failed'
	];

	// DISABLED ALERT
	public function getDisabledName($feedName, $alertName): string
    {
		return 'mmp_alert_disabled_' . $feedName . '_' . $alertName;
	}

	public function isAlertDisabled($feedName, $alertName) {
		$name = $this->getDisabledName($feedName, $alertName);

		return get_option($name, 0);
	}

	public function setAlertDisabled($feedName, $alertName): bool
    {
		$name = $this->getDisabledName($feedName, $alertName);

		return update_option($name, 1, true);
	}

	// DISABLED SECTION

	public function getDisabledSectionName($sectionName): string
    {
		return 'mmp_alert_section_disabled' . '_' . $sectionName;
	}

	public function isSectionDisabled($sectionName) {
		$name = $this->getDisabledSectionName($sectionName);

		return get_option($name, 0);
	}

	public function setSectionDisabled($sectionName): bool
    {
		$name = $this->getDisabledSectionName($sectionName);

		return update_option($name, 1, true);
	}

	// ERRORS
	public function getErrorName($feedName, $sectionName, $alertName): string
    {
		return 'mmp_alert_error_' . $feedName . '_' . $sectionName . '_' . $alertName;
	}

	public function getSectionByFeed($feedName)
	{
		return self::FEED_TO_SECTION[$feedName];
	}

	public function setErrorInactive($feedName, $alertName): bool
    {
		$sectionName = $this->getSectionByFeed($feedName);
		$name = $this->getErrorName($feedName, $sectionName, $alertName);

		return update_option($name, 0, true);
	}

	public function setErrorActive($feedName, $alertName): bool
    {
		$sectionName = $this->getSectionByFeed($feedName);
		$name = $this->getErrorName($feedName, $sectionName, $alertName);

		return update_option($name, 1, true);
	}

	public function getFeedErrors($feedName): array
    {
		$sectionName = $this->getSectionByFeed($feedName);

		$activeErrors = [];

		foreach(self::ALERT_NAMES as $alert) {
			$alertName = $this->getErrorName($feedName, $sectionName, $alert);

			if(get_option($alertName, 0) == 1) {
				$isNotHidden = !$this->isAlertDisabled($feedName, $alert);

				// Error is not hidden by user
				if ($isNotHidden) {
					$activeErrors[] = $alert;
				}
			}
		}

		return $activeErrors;
	}

	// Theres a function that set these variables base on specific conditions
	public function getMergadoErrors(): array
    {
		$errors = ['total' => 0];

		foreach(self::FEED_TO_SECTION as $feedName => $sectionName) {
		    if (!isset($errors[$sectionName])) {
                $errors[$sectionName] = 0;
            }

			foreach(self::ALERT_NAMES as $alert) {
				$alertName = $this->getErrorName($feedName, $sectionName, $alert);

				$hasError = get_option($alertName, 0);

				// Is error active
				if ($hasError) {
					$isNotHidden = !$this->isAlertDisabled($feedName, $alert);

					// Error is not hidden by user
					if ($isNotHidden) {
						$errors['total']++;
						$errors[$sectionName]++;
					}
				}
			}
		}

		return $errors;
	}

	public function checkIfErrorsShouldBeActive(): void
    {
		// Adding error if feeds exist and not updated for 24 hours
		$this->checkIfFeedsUpdated();
	}

	public function checkIfFeedsUpdated(): void
    {
        $productFeed = new ProductFeed();
		$categoryFeed = new CategoryFeed();
		$stockFeed = new StockFeed();
        $customerFeed = new CustomerFeed();

		if ($productFeed->isFeedExist() && $this->isTimestampOlderThan24hours($productFeed->getLastFeedChangeTimestamp())) {
			$this->setErrorActive('product', self::ALERT_NAMES['NO_FEED_UPDATE']);
		} else {
			$this->setErrorInactive('product', self::ALERT_NAMES['NO_FEED_UPDATE']);
		}

		if ($categoryFeed->isFeedExist() && $this->isTimestampOlderThan24hours($categoryFeed->getLastFeedChangeTimestamp())) {
			$this->setErrorActive('category', self::ALERT_NAMES['NO_FEED_UPDATE']);
		} else {
			$this->setErrorInactive('category', self::ALERT_NAMES['NO_FEED_UPDATE']);
		}

        if ($customerFeed->isFeedExist() && $this->isTimestampOlderThan24hours($customerFeed->getLastFeedChangeTimestamp())) {
            $this->setErrorActive('customer', self::ALERT_NAMES['NO_FEED_UPDATE']);
        } else {
            $this->setErrorInactive('customer', self::ALERT_NAMES['NO_FEED_UPDATE']);
        }

		if ($stockFeed->isFeedExist() && $this->isTimestampOlderThan24hours($stockFeed->getLastFeedChangeTimestamp())) {
			$this->setErrorActive('stock', self::ALERT_NAMES['NO_FEED_UPDATE']);
		} else {
			$this->setErrorInactive('stock', self::ALERT_NAMES['NO_FEED_UPDATE']);
		}
	}

	public function isTimestampOlderThan24hours($timestamp): bool
    {
		return strtotime('+1 day', $timestamp) < time();
	}
}
