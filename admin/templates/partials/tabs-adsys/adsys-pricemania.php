<?php

use Mergado\Service\External\Pricemania\PricemaniaService;

$pricemaniaService = PricemaniaService::getInstance();
?>

<div class="card full">
    <h3><?php echo __('Pricemania', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="<?php echo $pricemaniaService::ACTIVE; ?>"><?php echo __('Active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox"
                       id="<?php echo $pricemaniaService::ACTIVE; ?>"
                       name="<?php echo $pricemaniaService::ACTIVE; ?>"
                       data-mmp-check-main="pricemania"
                       data-mmp-activity-check-checkbox="true"
                       <?php if ($pricemaniaService->getActive() === 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo $pricemaniaService::ID; ?>"><?php echo __('Pricemania shop ID', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo $pricemaniaService::ID; ?>"
                       name="<?php echo $pricemaniaService::ID; ?>" data-mmp-check-field="pricemania"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $pricemaniaService->getId(); ?>">
                <br><small
                        class="badge badge_question"><?php echo __('Your unique Store ID from Pricemania.', 'mergado-marketing-pack') ?></small>
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
