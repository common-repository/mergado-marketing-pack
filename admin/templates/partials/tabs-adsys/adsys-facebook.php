<?php

use Mergado\Service\External\Facebook\FacebookService;

$facebookClass = FacebookService::getInstance();
?>

<div class="card full">
    <h3><?php echo __('Facebook Pixel', 'mergado-marketing-pack') ?></h3>
    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="facebook-form-active"><?php echo __('Active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="facebook-form-active" name="facebook-form-active"
                       data-mmp-check-main="facebook-active" data-mmp-activity-check-checkbox="true"
                       <?php if ($facebookClass->getActive() === 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="facebook-form-pixel"><?php echo __('Facebook pixel ID', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="facebook-form-pixel" name="facebook-form-pixel"
                       data-mmp-check-field="facebook-active"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $facebookClass->getCode() ?>">
                <br><small
                        class="badge badge_question"><?php echo __('Pixel ID can be found in your Facebook Business Manager. Go to Events Manager > Add new data feed > Facebook pixel. Pixel ID is displayed below the title on the Overview page at the top left.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="facebook-vat-included"><?php echo __('With VAT', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="facebook-vat-included" name="facebook-vat-included"
                       data-mmp-check-field="facebook-active"
                       <?php if ($facebookClass->isConversionWithVat()){ ?>checked="checked"<?php } ?>>
                <br><small
                        class="badge badge_info"><?php echo __('Choose whether the conversion value will be sent with or without VAT.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php echo __('Save', 'mergado-marketing-pack') ?>"
               name="submit-save">
    </p>
</div>
