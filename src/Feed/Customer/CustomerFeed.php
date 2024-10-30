<?php

/**
 * NOTICE OF LICENSE.
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    www.mergado.cz
 * @copyright 2016 Mergado technologies, s. r. o.
 * @license   LICENSE.txt
 */

namespace Mergado\Feed\Customer;

use Automattic\WooCommerce\Admin\API\Reports\Customers\DataStore;
use DateTime;
use DOMDocument;
use DOMException;
use Exception;
use Mergado\Exception\CronRunningException;
use Mergado\Feed\BaseFeed;
use WC_Customer;

class CustomerFeed extends BaseFeed
{
    const FEED_NAME = 'customer';
    const FEED_SECTION = 'other';

    public function __construct()
    {
        parent::__construct(
            self::FEED_NAME,
            self::FEED_SECTION,
            $this->getTotalCustomers(),
            'http://www.mergado.com/ns/customer/1.0',
            1500
        );
    }

    /**
     * @throws CronRunningException
     * @throws DOMException
     */
    public function generateXML($force = false): string
    {
        try {
            $now = new DateTime();
            $this->createNecessaryDirs();

            if ($this->isFeedLocked($now) && !$force) {
                $this->logger->info('FEED LOCKED - generating process can\'t proceed', $this->logContext);
                throw new CronRunningException();
            } else {
                $this->setFeedLocked($now);
                $customersPerStep = $this->getItemsPerStep();

                $currentFilesCount = $this->getCurrentTempFilesCount();
                $start = $this->getStart($currentFilesCount);

                // If no temporary files, reset generating
                if ($start === 1) {
                    $this->resetFeedGenerating();
                }

                $customerList = $this->getCustomers($start, $customersPerStep);

                // Step generating
                if ($this->isPartial($customersPerStep, $customerList)) {
                    $file = $this->tmpOutputDir . ($currentFilesCount) . '.xml';

                    $this->logger->info('Generator started - step ' . $currentFilesCount, $this->logContext);
                    $this->createXML($file, $start, $customersPerStep, $customerList);
                    $this->logger->info('Generator ended - step ' . $currentFilesCount, $this->logContext);
                    $this->logger->info('Generator saved XML file - step ' . $currentFilesCount, $this->logContext);

                    $this->increaseGenerationStep();
                    $this->unlockFeed();

                    return 'stepGenerated';
                    // Normal generating
                } else if ($this->isNormal($customersPerStep, $customerList)) {
                    $file = $this->xmlOutputDir . $this->getFeedFileName();

                    $this->logger->info('Generator started', $this->logContext);
                    $this->createXML($file);
                    $this->logger->info('Generator ended', $this->logContext);
                    $this->logger->info('Generator saved XML file', $this->logContext);

                    $this->unlockFeed();

                    return 'fullGenerated';
                    // Merge
                } else {
                    $this->mergeTemporaryFiles();
                    $this->unlockFeed();
                    $this->saveFullFeedGenerationCount();

                    return 'merged';
                }
            }
        } catch (CronRunningException $e) {
            throw $e;
        } catch (Exception $e) {
            $this->logger->error('Exception during feed generation: ' . $e, $this->logContext);

            throw $e;
        }
    }

    /**
     * @throws DOMException
     */
    private function createXML($file, $start = null, $limit = null, $customers = null) : void
    {
        if ($customers === null) {
            $customers = $this->getCustomers($start, $limit);
        }

        $xml = new DOMDocument('1.0', 'UTF-8');
        $channel = $xml->createElement('CHANNEL');
        $channel->setAttribute('xmlns', $this->feedVersion);
        $channel->appendChild($xml->createElement('link', get_home_url(get_current_blog_id())));
        $channel->appendChild($xml->createElement('generator', 'mergado.woocommerce.marketingpack.' . str_replace('.', '_', PLUGIN_VERSION)));

        foreach ($customers as $customer) {
            $customerFeedItem = $this->getCustomerFeedItem($customer);

            if ($customerFeedItem) {
                if ($itemXml = $customerFeedItem->getItemXml()) {
                    $importedNode = $xml->importNode($itemXml->getElementsByTagName('ITEM')[0], true);
                    $channel->appendChild($importedNode);
                }
            }
        }

        $xml->appendChild($channel);

        $xml->save($file);
    }

    /*******************************************************************************************************************
     * MERGE
     *******************************************************************************************************************/

    /**
     * Merge files, create XML and delete temporary files
     */
    protected function mergeTemporaryFiles(): bool
    {
        $this->logger->info('Merging XML files', $this->logContext);

        return parent::mergeTemporaryFiles();
    }

    /*******************************************************************************************************************
     * GET CUSTOMERS
     *******************************************************************************************************************/

    public function getCustomers($start, $stepCustomer): array
    {
        $offset = ($start - 1) * $stepCustomer;

        global $wpdb;
        $table_name = DataStore::get_db_table_name();
        $query = "SELECT * FROM $table_name";

        if ($stepCustomer > 0) {
            $query .= " LIMIT $stepCustomer OFFSET $offset";
        }

        return $wpdb->get_results($query);
    }

    public function getTotalCustomers(): int
    {
        global $wpdb;
        $table_name = DataStore::get_db_table_name();
        $query = "SELECT COUNT(*) FROM $table_name";

        return $wpdb->get_var($query);
    }

    public function getCustomerFeedItem($customer)
    {
        try {
            $customerFeedItem = new CustomerFeedItem();
            $woocommerceCustomer = new WC_Customer($customer->customer_id);

            // If exists
            if ($woocommerceCustomer->get_id() !== 0) {
                $order = wc_get_customer_last_order($woocommerceCustomer->get_id());

                // If user has any orders, add him to feed
                if ($order) {
                    $customerFeedItem
                        ->setCustomerId($woocommerceCustomer->get_id())
                        ->setCustomerFirstName($woocommerceCustomer->get_first_name())
                        ->setCustomerLastName($woocommerceCustomer->get_last_name())
                        ->setCustomerEmail($woocommerceCustomer->get_email())
                        ->setBillingFirstName($woocommerceCustomer->get_billing_first_name())
                        ->setBillingLastName($woocommerceCustomer->get_billing_last_name())
                        ->setBillingCompany($woocommerceCustomer->get_billing_company())
                        ->setBillingAddress1($woocommerceCustomer->get_billing_address_1())
                        ->setBillingAddress2($woocommerceCustomer->get_billing_address_2())
                        ->setBillingCity($woocommerceCustomer->get_billing_city())
                        ->setBillingZip($woocommerceCustomer->get_billing_postcode())
                        ->setBillingCountry($woocommerceCustomer->get_billing_country())
                        ->setBillingState($woocommerceCustomer->get_billing_state())
                        ->setBillingEmail($woocommerceCustomer->get_billing_email())
                        ->setBillingPhone($woocommerceCustomer->get_billing_phone())
                        ->setShippingFirstName($woocommerceCustomer->get_shipping_first_name())
                        ->setShippingLastName($woocommerceCustomer->get_shipping_last_name())
                        ->setShippingCompany($woocommerceCustomer->get_shipping_company())
                        ->setShippingAddress1($woocommerceCustomer->get_shipping_address_1())
                        ->setShippingAddress2($woocommerceCustomer->get_shipping_address_2())
                        ->setShippingCity($woocommerceCustomer->get_shipping_city())
                        ->setShippingZip($woocommerceCustomer->get_shipping_postcode())
                        ->setShippingCountry($woocommerceCustomer->get_shipping_country())
                        ->setShippingState($woocommerceCustomer->get_shipping_state())
                        ->setShippingPhone($woocommerceCustomer->get_shipping_phone());
                } else {
                    return false;
                }
            } else {
                // Get data from last order
                $lastOrder = $this->getLastCustomerOrder($customer->email);

                // Guest user without order has no data
                if ($lastOrder) {
                    $customerFeedItem
                        ->setCustomerId($customer->customer_id)
                        ->setCustomerFirstName($customer->first_name)
                        ->setCustomerLastName($customer->last_name)
                        ->setCustomerEmail($customer->email)
                        ->setBillingFirstName($lastOrder->get_billing_first_name())
                        ->setBillingLastName($lastOrder->get_billing_last_name())
                        ->setBillingCompany($lastOrder->get_billing_company())
                        ->setBillingAddress1($lastOrder->get_billing_address_1())
                        ->setBillingAddress2($lastOrder->get_billing_address_2())
                        ->setBillingCity($lastOrder->get_billing_city())
                        ->setBillingZip($lastOrder->get_billing_postcode())
                        ->setBillingCountry($lastOrder->get_billing_country())
                        ->setBillingState($lastOrder->get_billing_state())
                        ->setBillingEmail($lastOrder->get_billing_email())
                        ->setBillingPhone($lastOrder->get_billing_phone())
                        ->setShippingFirstName($lastOrder->get_shipping_first_name())
                        ->setShippingLastName($lastOrder->get_shipping_last_name())
                        ->setShippingCompany($lastOrder->get_shipping_company())
                        ->setShippingAddress1($lastOrder->get_shipping_address_1())
                        ->setShippingAddress2($lastOrder->get_shipping_address_2())
                        ->setShippingCity($lastOrder->get_shipping_city())
                        ->setShippingZip($lastOrder->get_shipping_postcode())
                        ->setShippingCountry($lastOrder->get_shipping_country())
                        ->setShippingState($lastOrder->get_shipping_state())
                        ->setShippingPhone($lastOrder->get_shipping_phone());
                } else {
                    return false;
                }
            }

            return apply_filters('mergado_customer_feed__customer', $customerFeedItem, $woocommerceCustomer, $customer);

        } catch (Exception $e) {
            $this->logger->error('Exception during creation of CustomerFeedItem for customerId ' . $customer->customer_id . ': ' . $e, $this->logContext);

            // Let the feed finish if some customer is broken.
            return false;
        }
    }

    public function getLastCustomerOrder($customerEmail)
    {
        $latestOrder = wc_get_orders([
            'customer' => $customerEmail,
            'orderby' => 'created',
            'order' => 'DESC',
            'limit' => 1,
        ]);

        if ($latestOrder && count($latestOrder) > 0) {
            return $latestOrder[0];
        }

        return false;
    }
}
