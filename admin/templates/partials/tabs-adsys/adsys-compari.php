<?php

use Mergado\Service\External\ArukeresoFamily\Compari\CompariService;

$compariService = CompariService::getInstance();
?>

<div class="card full">
    <h3><?php echo __('Compari Trusted Shop', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="<?php echo CompariService::ACTIVE ?>">
                    <?php echo __('Enable Trusted Shop', 'mergado-marketing-pack') ?>
                </label>
            </th>
            <td>
                <input type="checkbox" id="<?php echo CompariService::ACTIVE ?>"
                       name="<?php echo CompariService::ACTIVE ?>"
                       data-mmp-check-main="<?php echo CompariService::ACTIVE ?>"
                       data-mmp-activity-check-checkbox="true"
                       <?php if ($compariService->isActive()){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo CompariService::WEB_API_KEY ?>">
                    <?php echo __('WebAPI', 'mergado-marketing-pack') ?>
                </label>
            </th>
            <td>
                <input type="text" id="<?php echo CompariService::WEB_API_KEY ?>"
                       name="<?php echo CompariService::WEB_API_KEY ?>"
                       data-mmp-check-field="<?php echo CompariService::ACTIVE ?>"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $compariService->getWebApiKey() ?>">
                <br><small
                        class="badge badge_question"><?php echo __('You will find the WebAPI key in the Compari portal under Megbízható Bolt Program > Csatlakozás > Compari WebAPI kulcs', 'mergado-marketing-pack') ?></small>
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
            $enUsValue = $compariService->getOptOut('en_US');
            $defaultValue = CompariService::DEFAULT_OPT;

            if (trim($enUsValue) == '') {
                $enUsValue = $defaultValue;
            }
            ?>

            <th>
                <label for="<?php echo CompariService::OPT_OUT . 'en_US' ?>">
                    <?php echo __('en_US', 'mergado-marketing-pack') ?>
                </label>
            </th>

            <td colspan="2">
                <textarea
                        id="<?php echo CompariService::OPT_OUT . 'en_US' ?>"
                        name="<?php echo CompariService::OPT_OUT . 'en_US' ?>"
                        placeholder="<?php echo __('Insert your text for this language', 'mergado-marketing-pack') ?>"
                        data-mmp-check-field="<?php echo CompariService::ACTIVE ?>"
                ><?php echo $enUsValue ?></textarea>
                <br><small
                        class="badge badge_info"><?php echo __('English text will be used as default value if any other language won\'t be filled.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <?php foreach (get_available_languages() as $lang): ?>
            <tr>
                <th>
                    <label for="<?php echo CompariService::OPT_OUT . $lang ?>">
                        <?php echo __($lang, 'mergado-marketing-pack') ?>
                    </label>
                </th>
                <td colspan="2">
                    <textarea
                            id="<?php echo CompariService::OPT_OUT . $lang ?>"
                            name="<?php echo CompariService::OPT_OUT . $lang ?>"
                            placeholder="<?php echo __('Insert your text for this language', 'mergado-marketing-pack') ?>"
                            data-mmp-check-field="<?php echo CompariService::ACTIVE ?>"
                    ><?php echo stripslashes($compariService->getOptOut($lang)) ?></textarea>
                </td>
            </tr>
        <?php endforeach ?>

        <tr>
            <th>
                <label for="<?php echo CompariService::WIDGET_ACTIVE ?>">
                    <?php echo __('Enable widget Trusted Shop', 'mergado-marketing-pack') ?>
                </label>
            </th>
            <td><input type="checkbox" id="<?php echo CompariService::WIDGET_ACTIVE ?>"
                       name="<?php echo CompariService::WIDGET_ACTIVE ?>"
                       data-mmp-check-main="<?php echo CompariService::WIDGET_ACTIVE ?>"
                       data-mmp-check-field="<?php echo CompariService::ACTIVE ?>"
                       data-mmp-activity-check-checkbox="true"
                       <?php if ($compariService->isWidgetActive()){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo CompariService::WIDGET_DESKTOP_POSITION ?>">
                    <?php echo __('Widget position on desktop', 'mergado-marketing-pack'); ?>
                </label>
            </th>
            <td>
                <select name="<?php echo CompariService::WIDGET_DESKTOP_POSITION ?>"
                        id="<?php echo CompariService::WIDGET_DESKTOP_POSITION ?>"
                        data-mmp-check-field="<?php echo CompariService::WIDGET_ACTIVE ?>">
                    <?php foreach (CompariService::DESKTOP_POSITIONS() as $key => $data): ?>
                        <option
                            <?php if ($compariService->getWidgetDesktopPosition() === $data['id_option']){ ?>selected="selected"
                            <?php } ?>value="<?php echo $data['id_option'] ?>"><?php echo $data['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo CompariService::WIDGET_APPEARANCE_TYPE ?>">
                    <?php echo __('Appearance type on desktop', 'mergado-marketing-pack'); ?>
                </label>
            </th>
            <td>
                <select name="<?php echo CompariService::WIDGET_APPEARANCE_TYPE ?>"
                        id="<?php echo CompariService::WIDGET_APPEARANCE_TYPE ?>"
                        data-mmp-check-field="<?php echo CompariService::WIDGET_ACTIVE ?>">
                    <?php foreach (CompariService::APPEARANCE_TYPES() as $key => $data): ?>
                        <option
                            <?php if ($compariService->getWidgetAppearanceType() === $data['id_option']){ ?>selected="selected"
                            <?php } ?>value="<?php echo $data['id_option'] ?>"><?php echo $data['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo CompariService::WIDGET_MOBILE_POSITION ?>">
                    <?php echo __('Widget position on mobile', 'mergado-marketing-pack'); ?>
                </label>
            </th>
            <td>
                <select name="<?php echo CompariService::WIDGET_MOBILE_POSITION ?>"
                        id="<?php echo CompariService::WIDGET_MOBILE_POSITION ?>"
                        data-mmp-check-field="<?php echo CompariService::WIDGET_ACTIVE ?>">
                    <?php foreach (CompariService::getMobilePositionsConstant() as $key => $data): ?>
                        <option
                            <?php if ($compariService->getWidgetMobilePosition() === $data['id_option']){ ?>selected="selected"
                            <?php } ?>value="<?php echo $data['id_option'] ?>"><?php echo $data['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo CompariService::WIDGET_MOBILE_WIDTH ?>">
                    <?php echo __('Width on the mobile', 'mergado-marketing-pack') ?>
                </label>
            </th>
            <td><input type="text" id="<?php echo CompariService::WIDGET_MOBILE_WIDTH ?>"
                       name="<?php echo CompariService::WIDGET_MOBILE_WIDTH ?>"
                       data-mmp-check-field="<?php echo CompariService::WIDGET_ACTIVE ?>"
                       value="<?php echo $compariService->getWidgetMobileWidth() ?>"> px
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
