<?php

use Mergado\Service\External\Argep\ArgepService;

$argepService = ArgepService::getInstance();
?>

<div class="card full">
    <h3><?php echo __('Árgép', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="<?php echo ArgepService::CONVERSION_ACTIVE ?>"><?php echo __('Conversions active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="<?php echo ArgepService::CONVERSION_ACTIVE ?>"
                       name="<?php echo ArgepService::CONVERSION_ACTIVE ?>"
                       data-mmp-check-main="<?php echo ArgepService::CONVERSION_ACTIVE ?>"
                       data-mmp-activity-check-checkbox="true"
                       <?php if ($argepService->getConversionActive() === 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo ArgepService::CONVERSION_CODE ?>"><?php echo __('Code', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo ArgepService::CONVERSION_CODE ?>"
                       name="<?php echo ArgepService::CONVERSION_CODE ?>"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $argepService->getConversionCode() ?>">
                <!--                <br><small class="badge badge_question">-->
                <?php //echo __('Get the Conversion code in your Argep Account.', 'mergado-marketing-pack') ?><!--</small>-->
            </td>
        </tr>
        <tr>
            <th>
                <label for="adwords-form-conversion-label"><?php echo __('Conversion label', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo ArgepService::CONVERSION_LABEL ?>"
                       name="<?php echo ArgepService::CONVERSION_LABEL ?>"
                       data-mmp-check-field="<?php echo ArgepService::CONVERSION_ACTIVE ?>"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $argepService->getConversionLabel() ?>">
                <!--                <br><small class="badge badge_question">-->
                <?php //echo __('Get the Conversion code in your Argep Account.', 'mergado-marketing-pack') ?><!--</small>-->
            </td>
        </tr>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php echo __('Save', 'mergado-marketing-pack') ?>" name="submit-save">
    </p>
</div>
