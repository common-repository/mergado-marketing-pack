<?php

use Mergado\Service\External\ArukeresoFamily\Pazaruvaj\PazaruvajService;

$pazaruvajService = PazaruvajService::getInstance();
?>

<div class="card full">
    <h3><?php echo __('Pazaruvaj Trusted Shop', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="<?php echo PazaruvajService::ACTIVE ?>"><?php echo __('Enable Trusted Shop', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox"
                       id="<?php echo PazaruvajService::ACTIVE ?>"
                       name="<?php echo PazaruvajService::ACTIVE ?>"
                       data-mmp-check-main="<?php echo PazaruvajService::ACTIVE ?>"
                       data-mmp-activity-check-checkbox="true"
                       <?php if ($pazaruvajService->isActive()){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo PazaruvajService::WEB_API_KEY ?>"><?php echo __('WebAPI', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo PazaruvajService::WEB_API_KEY ?>"
                       name="<?php echo PazaruvajService::WEB_API_KEY ?>"
                       data-mmp-check-field="<?php echo PazaruvajService::ACTIVE ?>"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $pazaruvajService->getWebApiKey() ?>">
                <br><small
                        class="badge badge_question"><?php echo __('You will find the WebAPI key in the Pazaruvaj portal under Megbízható Bolt Program > Csatlakozás > Pazaruvaj WebAPI kulcs', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th><strong><?php echo __('Edit consent to the questionnaire', 'mergado-marketing-pack') ?></strong></th>
            <td>
                <small class="badge badge_question">
                    <?php echo __('Here you can edit the sentence of the consent to the sending of the questionnaire, displayed on the checkout page. This is an opt-out consent, ie the customer must confirm that he does not want to be involved in the program.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>


        <tr>
            <?php
            $enUsValue = $pazaruvajService->getOptOut('en_US');
            $defaultValue = PazaruvajService::DEFAULT_OPT;

            if (trim($enUsValue) == '') {
                $enUsValue = $defaultValue;
            }
            ?>

            <th>
                <label for="<?php echo PazaruvajService::OPT_OUT . 'en_US' ?>"><?php echo __('en_US', 'mergado-marketing-pack') ?></label>
            </th>

            <td colspan="2">
                <textarea
                        id="<?php echo PazaruvajService::OPT_OUT . 'en_US' ?>"
                        name="<?php echo PazaruvajService::OPT_OUT . 'en_US' ?>"
                        placeholder="<?php echo __('Insert your text for this language', 'mergado-marketing-pack') ?>"
                        data-mmp-check-field="<?php echo PazaruvajService::ACTIVE ?>"
                ><?php echo $enUsValue ?></textarea>
                <br><small
                        class="badge badge_info"><?php echo __('English text will be used as default value if any other language won\'t be filled.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <?php foreach (get_available_languages() as $lang): ?>
            <tr>
                <th>
                    <label for="<?php echo PazaruvajService::OPT_OUT . $lang ?>"><?php echo __($lang, 'mergado-marketing-pack') ?></label>
                </th>
                <td colspan="2">
                    <textarea
                            id="<?php echo PazaruvajService::OPT_OUT . $lang ?>"
                            name="<?php echo PazaruvajService::OPT_OUT . $lang ?>"
                            placeholder="<?php echo __('Insert your text for this language', 'mergado-marketing-pack') ?>"
                            data-mmp-check-field="<?php echo PazaruvajService::ACTIVE ?>"
                    ><?php echo stripslashes($pazaruvajService->getOptOut($lang)) ?></textarea>
                </td>
            </tr>
        <?php endforeach ?>

        <tr>
            <th>
                <label for="<?php echo PazaruvajService::WIDGET_ACTIVE ?>"><?php echo __('Enable widget Trusted Shop', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox"
                       id="<?php echo PazaruvajService::WIDGET_ACTIVE ?>"
                       name="<?php echo PazaruvajService::WIDGET_ACTIVE ?>"
                       data-mmp-check-main="<?php echo PazaruvajService::WIDGET_ACTIVE ?>"
                       data-mmp-check-field="<?php echo PazaruvajService::ACTIVE ?>"
                       data-mmp-activity-check-checkbox="true"
                       <?php if ($pazaruvajService->isWidgetActive()){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo PazaruvajService::WIDGET_DESKTOP_POSITION ?>"><?php echo __('Widget position on desktop', 'mergado-marketing-pack'); ?></label>
            </th>
            <td>
                <select name="<?php echo PazaruvajService::WIDGET_DESKTOP_POSITION ?>"
                        id="<?php echo PazaruvajService::WIDGET_DESKTOP_POSITION ?>"
                        data-mmp-check-field="<?php echo PazaruvajService::WIDGET_ACTIVE ?>">
                    <?php foreach (PazaruvajService::DESKTOP_POSITIONS() as $key => $data): ?>
                        <option
                            <?php if ($pazaruvajService->getWidgetDesktopPosition() === $data['id_option']){ ?>selected="selected"
                            <?php } ?>value="<?php echo $data['id_option'] ?>"><?php echo $data['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo PazaruvajService::WIDGET_APPEARANCE_TYPE ?>"><?php echo __('Appearance type on desktop', 'mergado-marketing-pack'); ?></label>
            </th>
            <td>
                <select name="<?php echo PazaruvajService::WIDGET_APPEARANCE_TYPE ?>"
                        id="<?php echo PazaruvajService::WIDGET_APPEARANCE_TYPE ?>"
                        data-mmp-check-field="<?php echo PazaruvajService::WIDGET_ACTIVE ?>">
                    <?php foreach (PazaruvajService::APPEARANCE_TYPES() as $key => $data): ?>
                        <option
                            <?php if ($pazaruvajService->getWidgetAppearanceType() === $data['id_option']){ ?>selected="selected"
                            <?php } ?>value="<?php echo $data['id_option'] ?>"><?php echo $data['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo PazaruvajService::WIDGET_MOBILE_POSITION ?>"><?php echo __('Widget position on mobile', 'mergado-marketing-pack'); ?></label>
            </th>
            <td>
                <select name="<?php echo PazaruvajService::WIDGET_MOBILE_POSITION ?>"
                        id="<?php echo PazaruvajService::WIDGET_MOBILE_POSITION ?>"
                        data-mmp-check-field="<?php echo PazaruvajService::WIDGET_ACTIVE ?>">
                    <?php foreach (PazaruvajService::getMobilePositionsConstant() as $key => $data): ?>
                        <option
                            <?php if ($pazaruvajService->getWidgetMobilePosition() === $data['id_option']){ ?>selected="selected"
                            <?php } ?>value="<?php echo $data['id_option'] ?>"><?php echo $data['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo PazaruvajService::WIDGET_MOBILE_WIDTH ?>"><?php echo __('Width on the mobile', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo PazaruvajService::WIDGET_MOBILE_WIDTH ?>"
                       name="<?php echo PazaruvajService::WIDGET_MOBILE_WIDTH ?>"
                       data-mmp-check-field="<?php echo PazaruvajService::WIDGET_ACTIVE ?>"
                       value="<?php echo $pazaruvajService->getWidgetMobileWidth() ?>"> px
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
