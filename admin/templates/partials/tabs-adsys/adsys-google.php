<?php

use Mergado\Service\External\Google\GoogleAds\GoogleAdsService;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\Ga4Service;
use Mergado\Service\External\Google\GoogleAnalytics\GoogleAnalyticsRefundService;
use Mergado\Service\External\Google\GoogleAnalytics\Universal\GaUniversalService;
use Mergado\Service\External\Google\GoogleReviews\GoogleReviewsService;
use Mergado\Service\External\Google\GoogleTagManager\GoogleTagManagerService;

$googleUniversalAnalyticsService = GaUniversalService::getInstance();
$ga4Service = Ga4Service::getInstance();
$googleAds = GoogleAdsService::getInstance();
$googleTagManager = GoogleTagManagerService::getInstance();


global $wpdb;

$query = 'SELECT DISTINCT postmeta.meta_key FROM ';
$query .= $wpdb->prefix . 'posts AS posts';
$query .= ' LEFT JOIN ' . $wpdb->prefix . 'postmeta AS postmeta ON posts.id = postmeta.post_id';
$query .= ' WHERE (posts.post_type="product" OR posts.post_type="product_variation") AND postmeta.meta_key NOT LIKE "\_%"';

$result = $wpdb->get_results($query, ARRAY_A);
$fields = [0 => '_sku'];

foreach ($result as $item) {
    $fields[] = $item['meta_key'];
}

$attributes = wc_get_attribute_taxonomies();
$select = [0 => '_sku'];

foreach ($attributes as $attr) {
    $select[] = $attr->attribute_name;
}

$fields = array_merge($fields, $select);
?>

<div class="card full">
    <h3><?php echo __('Google Ads', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="adwords-form-conversion-active"><?php echo __('Ads conversion active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="adwords-form-conversion-active" name="adwords-form-conversion-active"
                       data-mmp-check-main="adwords-conversion" data-mmp-activity-check-checkbox="true"
                       <?php if ($googleAds->getConversionActive() === 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo GoogleAdsService::ENHANCED_CONVERSION_ACTIVE ?>"><?php echo __('Ads enhanced conversions active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="<?php echo GoogleAdsService::ENHANCED_CONVERSION_ACTIVE ?>"
                       name="<?php echo GoogleAdsService::ENHANCED_CONVERSION_ACTIVE ?>"
                       data-mmp-check-field="adwords-conversion"
                       <?php if ($googleAds->getEnhancedConversionsActive() === 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="adwords-form-remarketing-active"><?php echo __('Ads remarketing active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="adwords-form-remarketing-active"
                       name="adwords-form-remarketing-active"
                       <?php if ($googleAds->getRemarketingActive() === 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="adwords-form-conversion-code"><?php echo __('Ads code', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="adwords-form-conversion-code" name="adwords-form-conversion-code"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $googleAds->getConversionCode() ?>">
                <br><small
                        class="badge badge_question"><?php echo __('Get the Conversion code in your Google Ads Account Administration > Tools & Settings > MEASUREMENT - Conversions > Add Conversion > Website. Create a new conversion, then click Install the tag yourself. The code is located in the “Global Site Tag” section and takes the form of AW-123456789.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="adwords-form-conversion-label"><?php echo __('Ads conversion label', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="adwords-form-conversion-label" name="adwords-form-conversion-label"
                       data-mmp-check-field="adwords-conversion"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $googleAds->getConversionLabel() ?>">
                <br><small
                        class="badge badge_question"><?php echo __('You can find the Conversion Label on the same page as the conversion code. The label is located in the “Event fragment” section of the send_to element, after the slash. For example, it has the form of /SqrGHAdS-MerfQC.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo GoogleAdsService::SHIPPING_PRICE_INCL ?>"><?php echo __('Values with shipping price', 'mergado-marketing-pack') ?></label>
            </th>
            <td colspan="2"><input type="checkbox"
                                   id="<?php echo GoogleAdsService::SHIPPING_PRICE_INCL ?>"
                                   name="<?php echo GoogleAdsService::SHIPPING_PRICE_INCL ?>"
                                   data-mmp-check-field="<?php echo GoogleAdsService::CONVERSION_ACTIVE ?>"
                                   <?php if ($googleAds->isShippingPriceIncluded()){ ?>checked="checked"<?php } ?>>
                <br><small
                        class="badge badge_info"><?php echo __('Choose whether the value of purchase will be with or without shipping.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo GoogleAdsService::CONVERSION_VAT_INCL ?>"><?php echo __('Product prices With VAT', 'mergado-marketing-pack') ?></label>
            </th>
            <td colspan="2"><input type="checkbox"
                                   id="<?php echo GoogleAdsService::CONVERSION_VAT_INCL ?>"
                                   name="<?php echo GoogleAdsService::CONVERSION_VAT_INCL ?>"
                                   data-mmp-check-field="<?php echo GoogleAdsService::CONVERSION_ACTIVE ?>"
                                   <?php if ($googleAds->isConversionWithVat()){ ?>checked="checked"<?php } ?>>
                <br><small
                        class="badge badge_info"><?php echo __('Choose whether the price of the products will be sent with or without VAT.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php echo __('Save', 'mergado-marketing-pack') ?>" name="submit-save">
    </p>
</div>

<div class="card full">
    <h3><?php echo __('Google analytics - gtag.js', 'mergado-marketing-pack') ?></h3>
    <p>Only Google Tag Manager or gtag.js should be active at a time</p>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="<?php echo GaUniversalService::ACTIVE ?>"><?php echo __('Active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="<?php echo GaUniversalService::ACTIVE ?>"
                       name="<?php echo GaUniversalService::ACTIVE ?>"
                       data-mmp-check-main="<?php echo GaUniversalService::ACTIVE ?>"
                       data-mmp-activity-check-checkbox="true"
                       <?php if ($googleUniversalAnalyticsService->getActive() === 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo GaUniversalService::CODE ?>"><?php echo __('Google Analytics tracking ID', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo GaUniversalService::CODE ?>"
                       name="<?php echo GaUniversalService::CODE ?>"
                       data-mmp-check-field="<?php echo GaUniversalService::ACTIVE ?>"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $googleUniversalAnalyticsService->getCode() ?>">
                <br><small
                        class="badge badge_question"><?php echo __('You can find your tracking ID in Google Analytics property > Admin > Property Settings, formatted as "UA-XXXXXXXXX-X', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo GaUniversalService::ECOMMERCE ?>"><?php echo __('Ecommerce tracking', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox"
                       id="<?php echo GaUniversalService::ECOMMERCE ?>"
                       data-mmp-check-main="<?php echo GaUniversalService::ECOMMERCE ?>"
                       data-mmp-check-field="<?php echo GaUniversalService::ACTIVE ?>"
                       name="<?php echo GaUniversalService::ECOMMERCE ?>"
                       <?php if ($googleUniversalAnalyticsService->getEcommerce() === 1){ ?>checked="checked"<?php } ?>>
                <br>
                <small class="badge badge_info"><?php echo __('Measurement of ecommerce transactions/purchases.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo GaUniversalService::ECOMMERCE_ENHANCED ?>"><?php echo __('Enhanced Ecommerce tracking', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox"
                       id="<?php echo GaUniversalService::ECOMMERCE_ENHANCED ?>"
                       data-mmp-check-field="<?php echo GaUniversalService::ECOMMERCE ?>"
                       name="<?php echo GaUniversalService::ECOMMERCE_ENHANCED ?>"
                       <?php if ($googleUniversalAnalyticsService->getEnhancedEcommerce() === 1){ ?>checked="checked"<?php } ?>>
                <br>
                <small class="badge badge_info"><?php echo __('Enhanced complex tracking of customer actions.', 'mergado-marketing-pack') ?></small>

            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo GaUniversalService::CONVERSION_VAT_INCL ?>"><?php echo __('Product prices With VAT', 'mergado-marketing-pack') ?></label>
            </th>
            <td colspan="2"><input type="checkbox" id="<?php echo GaUniversalService::CONVERSION_VAT_INCL ?>"
                                   name="<?php echo GaUniversalService::CONVERSION_VAT_INCL ?>"
                                   data-mmp-check-field="<?php echo GaUniversalService::ECOMMERCE ?>"
                                   <?php if ($googleUniversalAnalyticsService->isConversionWithVat()){ ?>checked="checked"<?php } ?>>
                <br><small class="badge badge_info"><?php echo __('Choose whether the price of the products will be sent with or without VAT.
This setting does not affect total revenue. The total revenue of the transaction is calculated including taxes and shipping costs according to the Google Analytics specification.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label><?php echo __('Refund', 'mergado-marketing-pack') ?></label>
            </th>
            <td>
                <small class="badge badge_info"><?php echo __('Select the order statuses at which the entire order will be refunded. When order status will change to the selected one, refund information will be send to Google Analytics. Note: Woocommerce automatically make Full refund when "Refunded" status is selected.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr style="background-color: #f6f7f7;">
            <th>
                <label for="<?php echo GoogleAnalyticsRefundService::STATUS ?>"><?php echo __('Order refund status', 'mergado-marketing-pack') ?></label>
            </th>
            <td>
                <table>
                    <tbody>
                    <?php $GaRefundClass = GoogleAnalyticsRefundService::getInstance(); ?>
                    <?php foreach (wc_get_order_statuses() as $key => $data): ?>
                        <tr>
                            <th class="px-0 pt-0 pb-5px fw-500"><?php echo $data ?></th>
                            <td class="px-0 pt-0 pb-5px">
                                <input type="checkbox" id="<?php echo GoogleAnalyticsRefundService::STATUS . $key ?>"
                                       name="<?php echo GoogleAnalyticsRefundService::STATUS . $key ?>"
                                       data-mmp-check-field="<?php echo GaUniversalService::ECOMMERCE ?>"
                                       <?php if ($GaRefundClass->isStatusActive($key) == 1){ ?>checked="checked"<?php } ?>>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php echo __('Save', 'mergado-marketing-pack') ?>" name="submit-save">
    </p>
</div>

<div class="card full">
    <h3><?php echo __('Google analytics 4 - gtag.js', 'mergado-marketing-pack') ?> <span class="label--beta">Beta</span>
    </h3>
    <p>Only Google Tag Manager or gtag.js should be active at a time</p>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="<?php echo Ga4Service::ACTIVE ?>"><?php echo __('Active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="<?php echo Ga4Service::ACTIVE ?>"
                       name="<?php echo Ga4Service::ACTIVE ?>"
                       data-mmp-check-main="<?php echo Ga4Service::ACTIVE ?>"
                       data-mmp-activity-check-checkbox="true"
                       <?php if ($ga4Service->getActive() === 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo Ga4Service::CODE ?>"><?php echo __('Google Analytics 4 tracking ID', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text"
                       id="<?php echo Ga4Service::CODE ?>"
                       name="<?php echo Ga4Service::CODE ?>"
                       data-mmp-check-field="<?php echo Ga4Service::ACTIVE ?>"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $ga4Service->getCode() ?>">
                <br><small
                        class="badge badge_question"><?php echo __('You can find your tracking ID in Google Analytics 4 property > Admin > Property Settings, formatted as "G-XXXXXXXXXX', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo Ga4Service::ECOMMERCE ?>"><?php echo __('Ecommerce tracking', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox"
                       id="<?php echo Ga4Service::ECOMMERCE ?>"
                       name="<?php echo Ga4Service::ECOMMERCE ?>"
                       data-mmp-check-main="<?php echo Ga4Service::ECOMMERCE ?>"
                       data-mmp-check-field="<?php echo Ga4Service::ACTIVE ?>"
                       <?php if ($ga4Service->getEcommerce() === 1){ ?>checked="checked"<?php } ?>>
                <br>
                <small class="badge badge_info"><?php echo __('Measurement of ecommerce transactions/purchases.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo Ga4Service::SHIPPING_PRICE_INCL ?>"><?php echo __('Values with shipping price', 'mergado-marketing-pack') ?></label>
            </th>
            <td colspan="2"><input type="checkbox"
                                   id="<?php echo Ga4Service::SHIPPING_PRICE_INCL ?>"
                                   name="<?php echo Ga4Service::SHIPPING_PRICE_INCL ?>"
                                   data-mmp-check-field="<?php echo Ga4Service::ECOMMERCE ?>"
                                   <?php if ($ga4Service->getShippingPriceIncluded() == 1){ ?>checked="checked"<?php } ?>>
                <br><small
                        class="badge badge_info"><?php echo __('Choose whether the value of view_cart, begin_checkout, add_payment_info, add_shipping_info and purchase will be with or without shipping.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo Ga4Service::CONVERSION_VAT_INCL ?>"><?php echo __('Product prices With VAT', 'mergado-marketing-pack') ?></label>
            </th>
            <td colspan="2"><input type="checkbox"
                                   id="<?php echo Ga4Service::CONVERSION_VAT_INCL ?>"
                                   name="<?php echo Ga4Service::CONVERSION_VAT_INCL ?>"
                                   data-mmp-check-field="<?php echo Ga4Service::ECOMMERCE ?>"
                                   <?php if ($ga4Service->isConversionWithVat()){ ?>checked="checked"<?php } ?>>
                <br><small class="badge badge_info"><?php echo __('Choose whether the price of the products will be sent with or without VAT.
This setting does not affect total revenue. The total revenue of the transaction is calculated including taxes and shipping costs according to the Google Analytics 4 specification.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label><?php echo __('Refund', 'mergado-marketing-pack') ?></label>
            </th>
            <td>
                <small class="badge badge_info"><?php echo __('Select the order statuses at which the entire order will be refunded. When order status will change to the selected one, refund information will be send to Google Analytics. Note: Woocommerce automatically make Full refund when "Refunded" status is selected.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr style="background-color: #f6f7f7;">
            <th>
                <label for="<?php echo Ga4Service::REFUND_STATUS ?>"><?php echo __('Order refund status', 'mergado-marketing-pack') ?></label>
            </th>
            <td>
                <table>
                    <tbody>
                    <?php foreach (wc_get_order_statuses() as $key => $data): ?>
                        <tr>
                            <th class="px-0 pt-0 pb-5px fw-500"><?php echo $data ?></th>
                            <td class="px-0 pt-0 pb-5px">
                                <input type="checkbox" id="<?php echo Ga4Service::REFUND_STATUS . $key ?>"
                                       name="<?php echo Ga4Service::REFUND_STATUS . $key ?>"
                                       data-mmp-check-field="<?php echo Ga4Service::ECOMMERCE ?>"
                                       <?php if ($ga4Service->isRefundStatusActive($key) == 1){ ?>checked="checked"<?php } ?>>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php echo __('Save', 'mergado-marketing-pack') ?>" name="submit-save">
    </p>
</div>


<div class="card full">
    <h3><?php echo __('Google Tag Manager', 'mergado-marketing-pack') ?></h3>
    <p>Only Google Tag Manager or gtag.js should be active at a time</p>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="<?php echo $googleTagManager::ACTIVE ?>"><?php echo __('Active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="<?php echo $googleTagManager::ACTIVE ?>"
                       name="<?php echo $googleTagManager::ACTIVE ?>"
                       data-mmp-check-main="gtm-active"
                       data-mmp-activity-check-checkbox="true"
                       <?php if ($googleTagManager->getActive() === 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo $googleTagManager::CODE ?>"><?php echo __('Google Tag Manager container ID', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo $googleTagManager::CODE ?>"
                       name="<?php echo $googleTagManager::CODE ?>" data-mmp-check-field="gtm-active"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $googleTagManager->getCode() ?>">
                <br><small
                        class="badge badge_question"><?php echo __('You can find your container ID in Tag Manager > Workspace. Near the top of the window, find your container ID, formatted as "GTM-XXXXXX".', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo $googleTagManager::ECOMMERCE_ACTIVE ?>"><?php echo __('Ecommerce tracking', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox"
                       id="<?php echo $googleTagManager::ECOMMERCE_ACTIVE ?>"
                       data-mmp-check-main="gtm-ecommerce"
                       data-mmp-check-field="gtm-active"
                       name="<?php echo $googleTagManager::ECOMMERCE_ACTIVE ?>"
                       <?php if ($googleTagManager->getEcommerceActive() === 1){ ?>checked="checked"<?php } ?>>
                <br>
                <small class="badge badge_info"><?php echo __('Measurement of ecommerce transactions/purchases.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo $googleTagManager::ECOMMERCE_ENHANCED_ACTIVE ?>"><?php echo __('Enhanced Ecommerce tracking', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox"
                       id="<?php echo $googleTagManager::ECOMMERCE_ENHANCED_ACTIVE ?>"
                       data-mmp-check-main="gtm-ecommerce-enhanced"
                       data-mmp-check-field="gtm-ecommerce"
                       data-mmp-check-subfiled="gtm-active"
                       name="<?php echo $googleTagManager::ECOMMERCE_ENHANCED_ACTIVE ?>"
                       <?php if ($googleTagManager->getEnhancedEcommerceActive() === 1){ ?>checked="checked"<?php } ?>>
                <br>
                <small class="badge badge_info"><?php echo __('Enhanced complex tracking of customer actions.', 'mergado-marketing-pack') ?></small>

            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo $googleTagManager::VIEW_LIST_ITEMS_COUNT ?>"><?php echo __('Max view_list_items', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="number" id="<?php echo $googleTagManager::VIEW_LIST_ITEMS_COUNT ?>"
                       name="<?php echo $googleTagManager::VIEW_LIST_ITEMS_COUNT ?>"
                       data-mmp-check-field="gtm-ecommerce-enhanced"
                       placeholder="<?php echo __('Insert number here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $googleTagManager->getViewListItemsCount(); ?>"
                       min="0"
                >
                <br><small
                        class="badge badge_question"><?php echo __('Set maximum of products sent in view_list_item event. Set 0 if you want to send all products on page.".', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="gtm-vat-included"><?php echo __('Product prices With VAT', 'mergado-marketing-pack') ?></label>
            </th>
            <td colspan="2"><input type="checkbox" id="gtm-vat-included" name="gtm-vat-included"
                                   data-mmp-check-field="gtm-pixel-active"
                                   <?php if ($googleTagManager->isConversionWithVat()){ ?>checked="checked"<?php } ?>>
                <br><small class="badge badge_info"><?php echo __('Choose whether the price of the products will be sent with or without VAT.
This setting does not affect total revenue. The total revenue of the transaction is calculated including taxes and shipping costs according to the Google Analytics specification.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php echo __('Save', 'mergado-marketing-pack') ?>" name="submit-save">
    </p>
</div>

<div class="card full">
    <h3><?php echo __('Google Customer Reviews', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="<?php echo GoogleReviewsService::OPT_IN_ACTIVE ?>"><?php echo __('Module active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="<?php echo GoogleReviewsService::OPT_IN_ACTIVE ?>"
                       name="<?php echo GoogleReviewsService::OPT_IN_ACTIVE ?>"
                       data-mmp-check-main="<?php echo GoogleReviewsService::OPT_IN_ACTIVE ?>"
                       data-mmp-activity-check-checkbox="true"
                       <?php if (get_option(GoogleReviewsService::OPT_IN_ACTIVE, 0) == 1){ ?>checked="checked"<?php } ?>>
                <br><small
                        class="badge badge_info"><?php echo __('Show google merchant opt-in on checkout page. To active Customer Reviews log into your Merchant Center > Growth > Manage programs > enable Reviews card.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo GoogleReviewsService::MERCHANT_ID ?>"><?php echo __('Merchant Id', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo GoogleReviewsService::MERCHANT_ID ?>"
                       name="<?php echo GoogleReviewsService::MERCHANT_ID ?>"
                       data-mmp-check-field="<?php echo GoogleReviewsService::OPT_IN_ACTIVE ?>"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo get_option(GoogleReviewsService::MERCHANT_ID, ''); ?>">
                <br><small
                        class="badge badge_question"><?php echo __('You can get this value from the Google Merchant Center. It\'s the same as your Google Merchant ID', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo GoogleReviewsService::OPT_IN_GTIN ?>"><?php echo __('Field for GTIN values', 'mergado-marketing-pack'); ?></label>
            </th>
            <td>
                <select name="<?php echo GoogleReviewsService::OPT_IN_GTIN ?>"
                        id="<?php echo GoogleReviewsService::OPT_IN_GTIN ?>"
                        data-mmp-check-field="<?php echo GoogleReviewsService::OPT_IN_ACTIVE ?>">
                    <?php foreach ($fields as $id => $data): ?>
                        <option
                            <?php if (get_option(GoogleReviewsService::OPT_IN_GTIN, 0) == $data){ ?>selected="selected"<?php } ?>
                            value="<?php echo $data ?>"><?php echo $data ?></option>
                    <?php endforeach ?>
                </select>
                <br><small
                        class="badge badge_question"><?php echo __('Select what field will be used as GTIN. "_sku" is default SKU value used by woocommerce', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo GoogleReviewsService::OPT_IN_DELIVERY_DATE ?>"><?php echo __('Days to send', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo GoogleReviewsService::OPT_IN_DELIVERY_DATE ?>"
                       name="<?php echo GoogleReviewsService::OPT_IN_DELIVERY_DATE ?>"
                       data-mmp-check-field="<?php echo GoogleReviewsService::OPT_IN_ACTIVE ?>"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo get_option(GoogleReviewsService::OPT_IN_DELIVERY_DATE, ''); ?>">
                <br><small
                        class="badge badge_question"><?php echo __('Number of days after ordering, when the email will be send to customers. Only numbers are accepted!', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo GoogleReviewsService::OPT_IN_POSITION ?>"><?php echo __('Opt-in position', 'mergado-marketing-pack'); ?></label>
            </th>
            <td>
                <select name="<?php echo GoogleReviewsService::OPT_IN_POSITION ?>"
                        id="<?php echo GoogleReviewsService::OPT_IN_POSITION ?>"
                        data-mmp-check-field="<?php echo GoogleReviewsService::OPT_IN_ACTIVE ?>">
                    <?php foreach (GoogleReviewsService::OPT_IN_POSITIONS_FOR_SELECT() as $key => $data): ?>
                        <option
                            <?php if (get_option(GoogleReviewsService::OPT_IN_POSITION, 0) == $data['id']){ ?>selected="selected"<?php } ?>
                            value="<?php echo $data['id'] ?>"><?php echo $data['name'] ?></option>
                    <?php endforeach ?>
                </select>
                <br><small
                        class="badge badge_question"><?php echo __('Select opt-in position.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo GoogleReviewsService::BADGE_ACTIVE ?>"><?php echo __('Badge active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="<?php echo GoogleReviewsService::BADGE_ACTIVE ?>"
                       name="<?php echo GoogleReviewsService::BADGE_ACTIVE ?>"
                       data-mmp-check-main="<?php echo GoogleReviewsService::BADGE_ACTIVE ?>"
                       data-mmp-check-field="<?php echo GoogleReviewsService::OPT_IN_ACTIVE ?>"
                       data-mmp-activity-check-checkbox="true"
                       <?php if (get_option(GoogleReviewsService::BADGE_ACTIVE, 0) == 1){ ?>checked="checked"<?php } ?>>
                <br><small
                        class="badge badge_info"><?php echo __('Show review rating badge on prefered location.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo GoogleReviewsService::BADGE_POSITION ?>"><?php echo __('Badge position', 'mergado-marketing-pack'); ?></label>
            </th>
            <td>
                <div>
                    <select name="<?php echo GoogleReviewsService::BADGE_POSITION ?>"
                            id="<?php echo GoogleReviewsService::BADGE_POSITION ?>"
                            data-mmp-check-field="<?php echo GoogleReviewsService::BADGE_ACTIVE ?>">
                        <?php foreach (GoogleReviewsService::BADGE_POSITIONS_FOR_SELECT() as $key => $data): ?>
                            <option
                                <?php if (get_option(GoogleReviewsService::BADGE_POSITION, 0) == $data['id']){ ?>selected="selected"<?php } ?>
                                value="<?php echo $data['id'] ?>"><?php echo $data['name'] ?></option>
                        <?php endforeach ?>
                    </select>
                    <br><small
                            class="badge badge_info"><?php echo __('Select badge position on page.', 'mergado-marketing-pack') ?></small>
                </div>
                <small
                        class="badge badge_info"
                        style="margin-left: 15px;"><?php echo __('Paste this line in your HTML at the location on the page where you would like the badge to appear.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo GoogleReviewsService::LANGUAGE ?>"><?php echo __('Language', 'mergado-marketing-pack'); ?></label>
            </th>
            <td>
                <select name="<?php echo GoogleReviewsService::LANGUAGE ?>"
                        id="<?php echo GoogleReviewsService::LANGUAGE ?>"
                        data-mmp-check-field="<?php echo GoogleReviewsService::OPT_IN_ACTIVE ?>">
                    <?php foreach (GoogleReviewsService::LANGUAGES() as $key => $data): ?>
                        <option
                            <?php if (get_option(GoogleReviewsService::LANGUAGE, 0) == $data['id']){ ?>selected="selected"<?php } ?>
                            value="<?php echo $data['id'] ?>"><?php echo $data['name'] ?></option>
                    <?php endforeach ?>
                </select>
                <br><small
                        class="badge badge_info"><?php echo __('Select language for opt-in form and badge', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php echo __('Save', 'mergado-marketing-pack') ?>" name="submit-save">
    </p>
</div>
