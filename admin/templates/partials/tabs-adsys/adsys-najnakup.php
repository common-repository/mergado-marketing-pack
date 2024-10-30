<?php

use Mergado\Service\External\NajNakup\NajNakupService;

$najNakupService = NajNakupService::getInstance();
?>

<div class="card full">
    <h3><?php echo __('NajNakup.sk', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="<?php echo $najNakupService::ACTIVE; ?>"><?php echo __('Active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox"
                       id="<?php echo $najNakupService::ACTIVE; ?>"
                       name="<?php echo $najNakupService::ACTIVE; ?>"
                       data-mmp-check-main="najnakup"
                       data-mmp-activity-check-checkbox="true"
                       <?php if ($najNakupService->getActive() === 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo $najNakupService::ID; ?>"><?php echo __('NajNakup shop ID', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo $najNakupService::ID; ?>" name="<?php echo $najNakupService::ID; ?>"
                       data-mmp-check-field="najnakup"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $najNakupService->getId(); ?>">
                <br><small
                        class="badge badge_question"><?php echo __('Your unique store ID for Najnakup.sk.', 'mergado-marketing-pack') ?></small>
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
