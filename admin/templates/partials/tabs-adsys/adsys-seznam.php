<?php

use Mergado\Service\External\Sklik\SklikService;
use Mergado\Service\External\Zbozi\ZboziService;

$zboziService = ZboziService::getInstance();
$sklikService = SklikService::getInstance();

?>

<div class="card full">
    <h3><?php echo __('Sklik', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="sklik-form-conversion-active"><?php echo __('Sklik conversion tracking', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox"
                       id="sklik-form-conversion-active"
                       name="sklik-form-conversion-active"
                       data-mmp-check-main="sklik-conversion"
                       data-mmp-activity-check-checkbox="true"
                       <?php if ($sklikService->getConversionActive()){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="sklik-form-conversion-code"><?php echo __('Sklik conversion code', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="sklik-form-conversion-code" name="sklik-form-conversion-code"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $sklikService->getConversionCode() ?>"
                       data-mmp-check-field="sklik-conversion"
                >
                <br><small
                        class="badge badge_question"><?php echo __('You can find the code in Sklik → Tools → Conversion Tracking → Conversion Detail / Create New Conversion. The code is in the generated HTML conversion code after: src = "// c.imedia.cz/checkConversion?c=CONVERSION CODE', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="sklik-form-conversion-value"><?php echo __('Sklik conversion value', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="number" id="sklik-form-conversion-value" name="sklik-form-conversion-value"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $sklikService->getConversionValue(); ?>"
                       data-mmp-check-field="sklik-conversion"
                >
                <br><small
                        class="badge badge_question"><?php echo __('Leave blank to fill the order value automatically. Total price excluding VAT and shipping is calculated.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="sklik-vat-included"><?php echo __('Sklik conversions with VAT', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="sklik-vat-included" name="sklik-vat-included"
                       data-mmp-check-field="sklik-conversion"
                       <?php if ($sklikService->getConversionVatIncluded()){ ?>checked="checked"<?php } ?>>
                <br><small
                        class="badge badge_info"><?php echo __('Choose whether the conversion value will be sent with or without VAT. Note: In the specification of conversion tracking, Sklik recommends the conversion value to be excluding VAT.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="sklik-form-retargeting-active"><?php echo __('Sklik retargeting', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox"
                       id="sklik-form-retargeting-active"
                       name="sklik-form-retargeting-active"
                       data-mmp-check-main="sklik-retargeting"
                       data-mmp-activity-check-checkbox="true"
                       <?php if ($sklikService->getRetargetingActive()){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="sklik-form-retargeting-id"><?php echo __('Sklik retargeting code', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="sklik-form-retargeting-id" name="sklik-form-retargeting-id"
                       data-mmp-check-field="sklik-retargeting"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $sklikService->getRetargetingId(); ?>">
                <br><small
                        class="badge badge_question"><?php echo __('The code can be found in Sklik → Tools → Retargeting → View retargeting code. The code is in the generated script after: var list_retargeting_id = RETARGETING CODE', 'mergado-marketing-pack') ?></small>
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
    <h3><?php echo __('Zboží.cz', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="zbozi-form-active"><?php echo __('Active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox"
                       id="zbozi-form-active"
                       name="zbozi-form-active"
                       data-mmp-check-main="zbozi-active"
                       data-mmp-activity-check-checkbox="true"
                       <?php if ($zboziService->getActive() === 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="zbozi-form-standard-active"><?php echo __('Enable standard conversion measuring', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="zbozi-form-standard-active" name="zbozi-form-standard-active"
                       data-mmp-check-field="zbozi-active"
                       <?php if ($zboziService->getStandardActive() === 1){ ?>checked="checked"<?php } ?>>
                <br><small
                        class="badge badge_info"><?php echo __('Unlike limited tracking, Standard Conversion Tracking allows you to keep track of the number and value of conversions, as well as conversion rate, cost per conversion, direct conversions, units sold, etc.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="zbozi-form-id"><?php echo __('Zbozi shop ID', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="zbozi-form-id" name="zbozi-form-id" data-mmp-check-field="zbozi-active"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $zboziService->getId() ?>">
                <br><small
                        class="badge badge_question"><?php echo __('You can find your unique Secret Key in admin page zbozi.cz > Branches > ESHOP > Conversion Tracking > Your unique Secret Key.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="zbozi-form-id"><?php echo __('Secret code', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="zbozi-form-secret-key" name="zbozi-form-secret-key"
                       data-mmp-check-field="zbozi-active"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $zboziService->getKey() ?>">
                <br><small
                        class="badge badge_question"><?php echo __('You can find your unique Secret Key in admin page zbozi.cz > Branches > ESHOP > Conversion Tracking > Your unique Secret Key.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th>
                <label for="zbozi-vat-included"><?php echo __('With VAT', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="zbozi-vat-included" name="zbozi-vat-included"
                       data-mmp-check-field="zbozi-active"
                       <?php if ($zboziService->isConversionWithVat()){ ?>checked="checked"<?php } ?>>
                <br><small
                        class="badge badge_info"><?php echo __('Choose whether the conversion value will be sent with or without VAT. Note: In the specification of conversion tracking, Zboží.cz recommends the price of the order and shipping to be including VAT.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th><strong><?php echo __('Edit consent to the questionnaire', 'mergado-marketing-pack') ?></strong></th>
            <td>
                <small class="badge badge_question">
                    <?php echo __('Here you can edit the text of the sentence of consent to the sending of the questionnaire, displayed in the checkout page. This is an opt-out consent, ie the customer must confirm that he does not want to be involved in the program.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo ZboziService::OPT_IN . 'en_US' ?>"><?php echo __('en_US', 'mergado-marketing-pack') ?></label>
            </th>

            <?php
            $enUsValue = stripslashes($zboziService->getOptOut('en_US'));
            $defaultValue = \Mergado\Service\External\Zbozi\ZboziService::DEFAULT_OPT;


            if (trim($enUsValue) == '') {
                $enUsValue = $defaultValue;
            }
            ?>

            <td colspan="2">
                <textarea
                        id="<?php echo ZboziService::OPT_IN . 'en_US' ?>"
                        name="<?php echo ZboziService::OPT_IN . 'en_US' ?>"
                        placeholder="<?php echo __('Insert your text for this language', 'mergado-marketing-pack') ?>"
                        data-mmp-check-field="zbozi-active"
                ><?php echo $enUsValue ?></textarea>
                <br><small
                        class="badge badge_info"><?php echo __('English text will be used as default value if any other language won\'t be filled.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <?php foreach (get_available_languages() as $lang): ?>
            <tr>
                <th>
                    <label for="<?php echo ZboziService::OPT_IN . $lang ?>"><?php echo __($lang, 'mergado-marketing-pack') ?></label>
                </th>
                <td colspan="2">
                    <textarea
                            id="<?php echo ZboziService::OPT_IN . $lang ?>"
                            name="<?php echo ZboziService::OPT_IN . $lang ?>"
                            placeholder="<?php echo __('Insert your text for this language', 'mergado-marketing-pack') ?>"
                            data-mmp-check-field="zbozi-active"
                    ><?php echo stripslashes($zboziService->getOptOut($lang)) ?></textarea>
                </td>
            </tr>
        <?php endforeach ?>

        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php echo __('Save', 'mergado-marketing-pack') ?>"
               name="submit-save">
    </p>
</div>
