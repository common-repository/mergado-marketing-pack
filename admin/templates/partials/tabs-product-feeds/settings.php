<?php

use Mergado\Feed\Product\ProductFeed;
use Mergado\Helper\TaxHelper;
use Mergado\Manager\DatabaseManager;
use Mergado\Service\Cron\CronService;
use Mergado\Service\Ean\EanService;
use Mergado\FeedVisibility\ProductFeedVisibilityService;

$productFeedService = ProductFeedVisibilityService::getInstance();

/**
 * Product feed form settings
 */
if (isset($_POST['save-products-settings'])) {
    //Product feed
    DatabaseManager::saveOptions($_POST, [], [ProductFeed::getUserItemCountPerStepDbName()]);

    //Feed VAT options
    DatabaseManager::saveOptions($_POST, [], [], [TaxHelper::VAT]);

    // WP CRON FORCED options - TAKE CARE ON TWO LOCATIONS !!
    DatabaseManager::saveOptions($_POST, [CronService::FORCE_ENABLE_CRON_DB_NAME]);

    //Ean options
    EanService::saveFields($_POST);

    //Visibility fields
    $productFeedService::saveFields($_POST);

    //Remove wizard params from URL
    wp_redirect('admin.php?page=' . $_GET['page'] . '&mmp-tab=' . $_GET['mmp-tab'] . '&flash=settingsSaved');
}
?>

<div class="card full">
    <form method="post" id="import-form" action="">
        <h1 class="mmp_h1"><?php echo __('Global settings for all product feeds', 'mergado-marketing-pack') ?></h1>

	    <?php
	    $alertData = ['alertSection' => 'product', 'feedName' => 'product'];

	    include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/settingsInfo.php';
	    ?>

        <div class="mmp_settingsBox">
            <!--            <h3>--><?php //_e('EAN code used in export', 'mergado-marketing-pack') ?><!--</h3>-->
            <!--            <p>-->
            <?php //_e('EAN added to product using selected plugin will be used in product feed results.', 'mergado-marketing-pack') ?><!--</p>-->
            <!--            <p>-->
            <?php //_e('Following selectbox provides supported plugins. In order to use their values, they must be installed and activated.', 'mergado-marketing-pack') ?><!--</p>-->
            <!--                    <p>-->
            <?php //_e('Our exports will use the VAT with the highest priority for the selected country.', 'mergado-marketing-pack') ?><!--</p>-->
            <table class="">
                <tbody>
                <tr>
                    <th>
                        <label for="select-ean" data-tippy-content="<?php echo __('Select which fields will be used as the value of the EAN element.', 'mergado-marketing-pack') ?>">
                            <?php echo esc_html__('Value for the <EAN> element', 'mergado-marketing-pack') ?>
                        </label>
                    </th>
                    <td>
                        <?php
                        $eanClass = new EanService();
                        ?>

                        <select name="<?php echo EanService::EAN_PLUGIN ?>" id="select-ean">

                            <?php
                            $eanOptions = EanService::getOptionsForSelect();
                            $eanSubOptions = EanService::getSuboptionsForSelect();
                            $eanSelectedOption = EanService::getSelectedPlugin();
                            $eanSubSelectedOption = EanService::getSelectedPluginField();
                            $eanIsSelectedPluginActive = $eanClass->getPluginIsActive();

                            if (!$eanIsSelectedPluginActive || (!$eanSelectedOption || $eanSelectedOption == 0)) { ?>
                                <option value="" data-has-fields="false" selected>
                                    <?php echo __('Disabled', 'mergado-marketing-pack') ?>
                                </option>
                            <?php } else { ?>
                                <option value="" data-has-fields="false">
                                    <?php echo __('Disabled', 'mergado-marketing-pack') ?>
                                </option>
                            <?php }

                            foreach ($eanOptions as $key => $option): ?>
                                <option value="<?php echo $key ?>"
                                    <?php
                                    if ($option['active'] && ($eanSelectedOption === $key)) {
                                        echo 'selected';
                                    }

                                    if (!$option['active']) {
                                        echo ' disabled';
                                    }
                                    ?>

                                        data-has-fields="<?php echo $eanOptions[$key]['hasFields'] ?>"
                                >
                                    <?php echo $option['name'] ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </td>
                </tr>
                <tr id="eanSubFieldLine" style="display:none;">
                    <th>
                        <label for="select-ean-subselect"><?php echo __('Field', 'mergado-marketing-pack') ?></label>
                    </th>
                    <td>
                        <select name="<?php echo EanService::EAN_PLUGIN_FIELD ?>" id="select-ean-subselect"
                                data-subselect-selected="<?php echo $eanSubSelectedOption ?>"
                        >
                            <?php
                            if ($eanSelectedOption):
                                foreach ($eanSubOptions[$eanSelectedOption] as $key => $option): ?>
                                    <option value="<?php echo $key ?>"
                                        <?php
                                        if ($eanIsSelectedPluginActive || ($eanSubSelectedOption === $key)) {
                                            echo 'selected';
                                        }
                                        ?>
                                    >
                                        <?php echo $option['name'] ?>
                                    </option>
                                <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </td>
                </tr>
                </tbody>
            </table>

            <script>
              document.addEventListener('DOMContentLoaded', function () {
                var selectbox = document.getElementById('select-ean');
                var subSelect = document.getElementById('select-ean-subselect');
                var subSelectSelected = subSelect.getAttribute('data-subselect-selected');
                var eanSubFieldLine = document.getElementById('eanSubFieldLine');

                window.eanSelectData = <?php echo json_encode($eanSubOptions) ?>;

                selectbox.addEventListener('change', function (e) {
                  changeSubselect(e);
                });

                function changeSubselect(e) {
                  var currentValue = e.target.value;
                  var currentData = window.eanSelectData[currentValue];

                  if (currentData && Object.keys(currentData).length > 0) {
                    Object.keys(currentData).forEach(function (key) {
                      var option = document.createElement('option');
                      option.setAttribute('value', key);

                      if (key === subSelectSelected) {
                        option.setAttribute('selected', 'selected');
                      }

                      option.text = currentData[key]['name'];
                      subSelect.add(option);
                    });

                    eanSubFieldLine.style.display = 'table-row';
                  } else {
                    subSelect.options.length = 0;
                    eanSubFieldLine.style.display = 'none';
                  }
                }

                <?php if ($eanIsSelectedPluginActive): ?>
                    if (subSelect.options.length && subSelect.options.length > 0) {
                      eanSubFieldLine.style.display = 'table-row';
                    }
                <?php endif; ?>
              });
            </script>
        </div>

        <div class="mmp_settingsBox">
            <!--            <p>-->
            <?php //_e('VAT percentage is taken by priority. If no VAT rate match your country code, then \'*\' is taken. If country code \'*\' is not set, then 0% is used.', 'mergado-marketing-pack') ?><!--</p>-->
            <!--                    <p>-->
            <?php //_e('Our exports will use the VAT with the highest priority for the selected country.', 'mergado-marketing-pack') ?><!--</p>-->
            <table class="">
                <tbody>
                <tr>
                    <th>
                        <label
                                data-tippy-content="<?php echo __('Vat rate is taken by priority. If no VAT rate matches your country code, then \'*\' is taken. If country code \'*\' is not set, then 0% is used.', 'mergado-marketing-pack') ?>"
                                for="feed-form-vat"><?php echo __('Country tax rule (VAT) used in exports', 'mergado-marketing-pack') ?></label>
                    </th>
                    <td>
                        <select name="<?php echo TaxHelper::VAT ?>" id="feed-form-vat">

                            <?php $rates = TaxHelper::getTaxRates();
                            foreach ($rates as $rate):
                                ?>
                                <option value="<?php echo $rate->tax_rate_country ?>"
                                    <?php
                                    if ($rate->tax_rate_country === get_option(TaxHelper::VAT)) {
                                        echo 'selected';
                                    }
                                    ?>
                                >
                                    <?php echo $rate->tax_rate_country === '' ? '*' : $rate->tax_rate_country ?>
                                </option>
                            <?php
                            endforeach
                            ?>
                        </select>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="mmp_settingsBox">
            <table class="">
                <tbody>
                    <tr>
                        <th style="vertical-align: baseline;">
                            <label data-tippy-content="<?php echo __('Select which products to export according to Visibility in WooCommerce. The Visibility value will be identified in the <code><&#8203;VISIBILITY&#8203;></code> element.', 'mergado-marketing-pack') ?>"
                                   for="feed-form-vat"><?php echo __('Export by Visibility', 'mergado-marketing-pack') ?></label>
                        </th>
                        <td style="text-align: left;">
                            <?php foreach($productFeedService->baseVisibilityOptions as $item):?>
                                <div class="mmp_checkbox--inline">
                                    <input type="checkbox"
                                           id="<?php echo $item['databaseName'] ?>"
                                           name="<?php echo $item['databaseName'] ?>"
                                           <?php if ($item['value'] === '1'){ ?>checked="checked"<?php } ?>>

                                    <label for="<?php echo $item['databaseName'] ?>">
                                        <?php echo $item['name']; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mmp_settingsBox">
            <table class="">
                <tbody>
                    <tr>
                        <th style="vertical-align: baseline;">
                            <label data-tippy-content="<?php echo __('Select which products to export according to Catalog visibility in WooCommerce. The Visibility value will be identified in the <code><&#8203;CATALOG_VISIBILITY&#8203;></code> element.', 'mergado-marketing-pack') ?>"
                                   for="feed-form-vat"><?php echo __('Export by Catalog visibility', 'mergado-marketing-pack') ?></label>
                        </th>
                        <td style="text-align: left;">
                            <?php foreach($productFeedService->catalogVisibilityOptions as $item):?>
                                <div class="mmp_checkbox--inline">
                                    <input type="checkbox"
                                           id="<?php echo $item['databaseName'] ?>"
                                           name="<?php echo $item['databaseName'] ?>"
                                           <?php if ($item['value'] === '1'){ ?>checked="checked"<?php } ?>>

                                    <label for="<?php echo $item['databaseName'] ?>">
                                        <?php echo $item['name']; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>


        <div class="mmp_advancedSettingsBox">
            <div class="mmp_advancedSettingsBox__header">
                <p><?php echo __('Advanced settings', 'mergado-marketing-pack') ?></p>
                <div class="mmp_advancedSettingsBox__toggler">
                    <svg>
                        <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . 'icons.svg#chevron-down' ?>"></use>
                    </svg>
                </div>
            </div>
            <div class="mmp_advancedSettingsBox__content">

                <div class="mmp_settingsBox">
                    <table>
                        <tbody>
                        <tr>
                            <th>
                                <label for="feed-form-products"
                                       data-tippy-content="<?php echo __('Changing the batch size could seriously effect the performance of your website. We advice against changing the batch size if you are unsure about its effects!<br><br>Default number is set to 1500 items per batch step.', 'mergado-marketing-pack') ?>"
                                ><?php echo __('Change the number of products per batch (Change only if advised by our support team)', 'mergado-marketing-pack') ?></label>
                            </th>
                            <td class="text-align: left;">
                                <input type="number" min="1" id="feed-form-products" name="feed-form-products"
                                       placeholder="<?php echo __('Insert number of products', 'mergado-marketing-pack') ?>"
                                       value="<?php echo get_option('feed-form-products', ''); ?>">
                                <br>
                                <small><?php echo __('Leave blank to generate the entire XML feed at once.', 'mergado-marketing-pack') ?></small>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mmp_settingsBox">
                    <table>
                        <tbody>
                        <tr>
                            <th>
                                <label for="<?php echo CronService::FORCE_ENABLE_CRON_DB_NAME ?>"
                                       data-tippy-content="<?php echo __('Checking this checkbox will unlock the WP cron wizard even with:<br><br> <strong>DISABLED_WP_CRON = true in wp-config.php</strong><br><br> This feature is intended to be used with externaly enabled WP CRON script.', 'mergado-marketing-pack') ?>"
                                ><?php echo __('Unlock WP cron forms', 'mergado-marketing-pack') ?></label>
                            </th>
                            <td class="text-align: left;">
                                <input type="checkbox"
                                       id="<?php echo CronService::FORCE_ENABLE_CRON_DB_NAME ?>"
                                       name="<?php echo CronService::FORCE_ENABLE_CRON_DB_NAME ?>"
		                               <?php if (CronService::isWpCronForceEnabled()){ ?>checked="checked"<?php } ?>>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <div class="mmp_btnHolder mmp_btnHolder--right">
            <input type="submit" class="button mmp_btn__blue mmp_btn--wide"
                   value="<?php echo __('Save', 'mergado-marketing-pack') ?>" name="save-products-settings">
        </div>
    </form>
</div>
