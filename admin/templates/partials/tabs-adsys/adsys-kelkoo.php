<?php

use Mergado\Service\External\Kelkoo\KelkooService;

$kelkooService = KelkooService::getInstance();

?>

<div class="card full">
    <h3><?php echo __('Kelkoo', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="<?php echo $kelkooService::ACTIVE ?>"><?php echo __('Active', 'mergado-marketing-pack') ?></label>
            </th>
            <td>
                <input type="checkbox"
                       id="<?php echo $kelkooService::ACTIVE ?>"
                       name="<?php echo $kelkooService::ACTIVE ?>"
                       data-mmp-check-main="kelkoo-active"
                       data-mmp-activity-check-checkbox="true"
                       <?php if ($kelkooService->getActive() === 1){ ?>checked="checked"<?php } ?> />
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo $kelkooService::COUNTRY ?>"><?php echo __('Kelkoo country', 'mergado-marketing-pack'); ?></label>
            </th>
            <td>
                <select name="<?php echo $kelkooService::COUNTRY ?>" id="<?php echo $kelkooService::COUNTRY ?>"
                        data-mmp-check-field="kelkoo-active"
                <?php foreach ($kelkooService::COUNTRIES as $key => $data): ?>
                    <option
                        <?php if (count($kelkooService->getCountry()) > 0 && $kelkooService->getCountry()['id_option'] == $data['id_option']){ ?>selected="selected"<?php } ?>
                        value="<?php echo $data['id_option'] ?>"><?php echo $data['name'] ?></option>
                <?php endforeach ?>
                </select>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo $kelkooService::ID; ?>"><?php echo __('Kelkoo merchant ID', 'mergado-marketing-pack'); ?></label>
            </th>
            <td>
                <input type="text" id="<?php echo $kelkooService::ID; ?>" name="<?php echo $kelkooService::ID; ?>"
                       data-mmp-check-field="kelkoo-active"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $kelkooService->getId() ?>">
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo $kelkooService::CONVERSION_VAT_INCL ?>"><?php echo __('With VAT', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="<?php echo $kelkooService::CONVERSION_VAT_INCL ?>"
                       name="<?php echo $kelkooService::CONVERSION_VAT_INCL ?>" data-mmp-check-field="kelkoo-active"
                       <?php if ($kelkooService->isConversionWithVat()){ ?>checked="checked"<?php } ?>>
                <br><small
                        class="badge badge_info"><?php echo __('Choose whether the conversion value will be sent with or without VAT.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php echo __('Save', 'mergado-marketing-pack') ?>" name="submit-save">
    </p>
</div>
