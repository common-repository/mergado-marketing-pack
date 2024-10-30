<?php

use Mergado\Service\External\Etarget\EtargetService;

$etargetService = EtargetService::getInstance();
?>


<div class="card full">
    <h3><?php echo __('Etarget', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="<?php echo $etargetService::ACTIVE; ?>"><?php echo __('Active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="<?php echo $etargetService::ACTIVE; ?>"
                       name="<?php echo $etargetService::ACTIVE; ?>" data-mmp-check-main="etarget"
                       data-mmp-activity-check-checkbox="true"
                       <?php if ($etargetService->getActive() === 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo $etargetService::HASH; ?>"><?php echo __('ETARGET hash', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo $etargetService::HASH; ?>" name="<?php echo $etargetService::HASH; ?>"
                       data-mmp-check-field="etarget"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $etargetService->getHash(); ?>"></td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo $etargetService::ID; ?>>"><?php echo __('ETARGET ID', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo $etargetService::ID; ?>" name="<?php echo $etargetService::ID; ?>"
                       data-mmp-check-field="etarget"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $etargetService->getId(); ?>"></td>
        </tr>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php echo __('Save', 'mergado-marketing-pack') ?>"
               name="submit-save">
    </p>
</div>
