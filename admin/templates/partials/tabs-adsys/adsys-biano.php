<?php

use Mergado\Service\External\Biano\BianoService;
use Mergado\Service\External\Biano\BianoStarService;

$bianoService = BianoService::getInstance();
$bianoStarService = BianoStarService::getInstance();
?>

<div class="card full">
    <h3><?php echo __('Biano', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="<?php echo BianoService::ACTIVE ?>"><?php echo __('Active', 'mergado-marketing-pack') ?></label>
            </th>
            <td>
                <input type="checkbox"
                       id="<?php echo BianoService::ACTIVE ?>"
                       name="<?php echo BianoService::ACTIVE ?>"
                       data-mmp-check-main="<?php echo BianoService::ACTIVE ?>"
                       data-mmp-activity-check-checkbox="true"
                       <?php if ($bianoService->getActive() === 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>

        <?php
        foreach (BianoService::LANGUAGES as $lang):
            $activeLangName = BianoService::getActiveLangName($lang);
            $activeMerchantId = BianoService::getMerchantIdName($lang);
            ?>

            <tr>
                <th>
                    <label for="<?php echo $activeLangName; ?>">
                        <?php echo __('Biano pixel', 'mergado-marketing-pack') ?> - <?php echo $lang; ?>
                    </label>
                </th>
                <td>
                    <input type="checkbox"
                           id="<?php echo $activeLangName; ?>"
                           name="<?php echo $activeLangName; ?>"
                           data-mmp-check-main="<?php echo BianoService::ACTIVE ?>-<?php echo $lang; ?>"
                           data-mmp-check-field="<?php echo BianoService::ACTIVE ?>"
                        <?php if ($bianoService->getActiveLang($lang) === 1) { ?>
                            checked="checked"
                        <?php } ?>>

                    <input type="text"
                           id="<?php echo $activeMerchantId; ?>"
                           name="<?php echo $activeMerchantId; ?>"
                           data-mmp-check-field="<?php echo BianoService::ACTIVE ?>-<?php echo $lang; ?>"
                           placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                           value="<?php echo $bianoService->getMerchantId($lang); ?>">
                </td>
            </tr>
        <?php endforeach ?>

        <tr>
            <th>
                <label for="biano-vat-included"><?php echo __('With VAT', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox"
                       id="biano-vat-included"
                       name="biano-vat-included"
                       data-mmp-check-field="<?php echo BianoService::ACTIVE ?>"
                    <?php if ($bianoService->getConversionVatIncluded() === 1) { ?>
                        checked="checked"
                    <?php } ?>
                >
                <br>
                <small class="badge badge_info">
                    <?php echo __('Choose whether the conversion value will be sent with or without VAT.', 'mergado-marketing-pack') ?>
                </small>
            </td>
        </tr>
        </tbody>
    </table>

    <p>
        <input type="submit"
               class="button button-primary button-large"
               value="<?php echo __('Save', 'mergado-marketing-pack') ?>"
               name="submit-save">
    </p>
</div>

<div class="card full">
    <h3><?php echo __('Biano Star', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="<?php echo BianoStarService::ACTIVE ?>">
                    <?php echo __('Active', 'mergado-marketing-pack') ?>
                </label>
            </th>
            <td>
                <input type="checkbox"
                       id="<?php echo BianoStarService::ACTIVE ?>"
                       name="<?php echo BianoStarService::ACTIVE ?>"
                       data-mmp-check-main="<?php echo BianoStarService::ACTIVE ?>"
                       data-mmp-check-field="<?php echo BianoService::ACTIVE ?>"
                       data-mmp-activity-check-checkbox="true"
                       <?php if ($bianoStarService->getActive() === 1){ ?>checked="checked"<?php } ?>>
                <br>
                <small class="badge badge_info">
                    <?php echo __('Biano Star is dependent on Biano Pixel. You must first activate the Pixel function and then Biano Star.', 'mergado-marketing-pack') ?>
                </small>
            </td>
        </tr>

        <tr>
            <th>
                <strong><?php echo __('Shipment in', 'mergado-marketing-pack') ?></strong>
            </th>
            <td>
                <small class="badge badge_question">
                    <?php echo __('Please enter number of days since placing the order until the shipment. In case customer places an order for products which are out of stock or on backorder, the latest date of shipment will be opt for.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo BianoStarService::SHIPMENT_IN_STOCK ?>">
                    <?php echo __('In stock', 'mergado-marketing-pack') ?>
                </label>
            </th>
            <td><input type="number"
                       id="<?php echo BianoStarService::SHIPMENT_IN_STOCK ?>"
                       name="<?php echo BianoStarService::SHIPMENT_IN_STOCK ?>"
                       data-mmp-check-field="<?php echo BianoStarService::ACTIVE ?>"
                       placeholder="<?php echo __('', 'mergado-marketing-pack') ?>"
                       value="<?php echo $bianoStarService->getShipmentInStock() ?>">
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo BianoStarService::SHIPMENT_BACKORDER ?>"><?php echo __('backorder', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="number" id="<?php echo BianoStarService::SHIPMENT_BACKORDER ?>"
                       name="<?php echo BianoStarService::SHIPMENT_BACKORDER ?>"
                       data-mmp-check-field="<?php echo BianoStarService::ACTIVE ?>"
                       placeholder="<?php echo __('', 'mergado-marketing-pack') ?>"
                       value="<?php echo $bianoStarService->getShipmentBackorder() ?>">
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo BianoStarService::SHIPMENT_OUT_OF_STOCK ?>">
                    <?php echo __('out of stock', 'mergado-marketing-pack') ?>
                </label>
            </th>
            <td>
                <input type="number" id="<?php echo BianoStarService::SHIPMENT_OUT_OF_STOCK ?>"
                       name="<?php echo BianoStarService::SHIPMENT_OUT_OF_STOCK ?>"
                       data-mmp-check-field="<?php echo BianoStarService::ACTIVE ?>"
                       placeholder="<?php echo __('', 'mergado-marketing-pack') ?>"
                       value="<?php echo $bianoStarService->getShipmentOutOfStock() ?>">
            </td>
        </tr>

        <tr>
            <th>
                <strong><?php echo __('Edit consent to the questionnaire', 'mergado-marketing-pack') ?></strong>
            </th>
            <td>
                <small class="badge badge_question">
                    <?php echo __('Here you can edit the sentence of the consent to the sending of the questionnaire, displayed on the checkout page. This is an opt-out consent, ie the customer must confirm that he does not want to be involved in the program.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>


        <tr>
            <?php
            $enUsValue = $bianoStarService->getOptOut('en_US');
            $defaultValue = BianoStarService::DEFAULT_OPT;

            if (trim($enUsValue) == '') {
                $enUsValue = $defaultValue;
            }
            ?>

            <th>
                <label for="<?php echo BianoStarService::OPT_OUT . 'en_US' ?>">
                    <?php echo __('en_US', 'mergado-marketing-pack') ?>
                </label>
            </th>

            <td colspan="2">
                <textarea
                        id="<?php echo BianoStarService::OPT_OUT . 'en_US' ?>"
                        name="<?php echo BianoStarService::OPT_OUT . 'en_US' ?>"
                        placeholder="<?php echo __('Insert your text for this language', 'mergado-marketing-pack') ?>"
                        data-mmp-check-field="<?php echo BianoStarService::ACTIVE ?>"
                ><?php echo $enUsValue ?></textarea>
                <br><small
                        class="badge badge_info"><?php echo __('English text will be used as default value if any other language won\'t be filled.', 'mergado-marketing-pack'); ?></small>
            </td>
        </tr>

        <?php foreach (get_available_languages() as $lang): ?>
            <tr>
                <th>
                    <label for="<?php echo BianoStarService::OPT_OUT . $lang ?>">
                        <?php echo __($lang, 'mergado-marketing-pack') ?>
                    </label>
                </th>
                <td colspan="2">
                    <textarea
                            id="<?php echo BianoStarService::OPT_OUT . $lang ?>"
                            name="<?php echo BianoStarService::OPT_OUT . $lang ?>"
                            placeholder="<?php echo __('Insert your text for this language', 'mergado-marketing-pack') ?>"
                            data-mmp-check-field="<?php echo BianoStarService::ACTIVE ?>"
                    ><?php echo stripslashes($bianoStarService->getOptOut($lang)) ?></textarea>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>

    <p>
        <input type="submit"
               class="button button-primary button-large"
               value="<?php echo __('Save', 'mergado-marketing-pack') ?>"
               name="submit-save">
    </p>
</div>
