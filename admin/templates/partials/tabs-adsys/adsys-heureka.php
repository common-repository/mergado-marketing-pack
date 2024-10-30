<?php

use Mergado\Service\External\Heureka\BaseHeurekaService;
use Mergado\Service\External\Heureka\HeurekaCzService;
use Mergado\Service\External\Heureka\HeurekaSkService;

$heurekaCzService = HeurekaCzService::getInstance();
$heurekaSkService = HeurekaSkService::getInstance();
?>

<!----------------------   CZ   ---------------------->

<div class="card full">
    <h3><?php echo __('Heureka.cz : Verified by customers', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>

        <!--   Verified by customers - ENABLER   -->
        <tr>
            <th>
                <label for="<?php echo HeurekaCzService::VERIFIED_ACTIVE ?>"><?php echo __('Enable verified by users', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="<?php echo HeurekaCzService::VERIFIED_ACTIVE ?>"
                       data-mmp-check-main="heureka-verified-cz"
                       data-mmp-activity-check-checkbox="true"
                       name="<?php echo HeurekaCzService::VERIFIED_ACTIVE ?>"

                       <?php if ($heurekaCzService->getVerifiedActive() === 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>


        <!--   Verified by customers - CODE   -->
        <tr>
            <th>
                <label for="<?php echo HeurekaCzService::VERIFIED_CODE ?>"><?php echo __('Verified by users code', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo HeurekaCzService::VERIFIED_CODE ?>"
                       name="<?php echo HeurekaCzService::VERIFIED_CODE ?>" data-mmp-check-field="heureka-verified-cz"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $heurekaCzService->getVerifiedCode() ?>">
                <br><small
                        class="badge badge_question"><?php echo __('You can find your store key in the Heureka account administration under Verified customers > Settings and questionnaire data > Secret Key for verified customers.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th>
                <label><?php echo __('Text of the customer\'s consent to sending the questionnaire', 'mergado-marketing-pack') ?></label>
            </th>
            <td>
                <a href="#mmp_consentText"
                   class="button button-primary button-large"><?php echo __('Edit consent text', 'mergado-marketing-pack') ?></a>
            </td>
        </tr>


        <!--   Verified by customers - WIDGET ENABLER   -->
        <tr>
            <th>
                <label for="<?php echo HeurekaCzService::WIDGET_ACTIVE ?>"><?php echo __('Enable CZ widget', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox"
                       id="<?php echo HeurekaCzService::WIDGET_ACTIVE ?>"
                       name="<?php echo HeurekaCzService::WIDGET_ACTIVE ?>"
                       data-mmp-check-main="heureka-verified-widget-cz"
                       data-mmp-activity-check-checkbox="true"
                       <?php if ($heurekaCzService->getWidgetActive() === 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>


        <!--   Verified by customers - WIDGET - ID   -->
        <tr>
            <th>
                <label for="<?php echo HeurekaCzService::WIDGET_ID ?>"><?php echo __('Widget ID', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo HeurekaCzService::WIDGET_ID ?>"
                       name="<?php echo HeurekaCzService::WIDGET_ID ?>"
                       data-mmp-check-field="heureka-verified-widget-cz"
                       placeholder="<?php echo __('Insert Widget Id', 'mergado-marketing-pack') ?>"
                       value="<?php echo $heurekaCzService->getWidgetId() ?>">
                <br><small class="badge badge_question"><?php echo __('The ID is the same as the Public Key for conversion tracking.
Or you can find the key of your widget in the Heureka account administration under the tab Verified customers > Settings and questionnaire data > Certificate icons Verified customers. The numeric code is in the embed code. It takes the form "... setKey\',\'330BD_YOUR_WIDGET_KEY_2A80\']); _ hwq.push\' ..."', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>


        <!--   Verified by customers - WIDGET - POSITION  -->
        <tr>
            <th>
                <label for="<?php echo HeurekaCzService::WIDGET_POSITION ?>"><?php echo __('Widget position', 'mergado-marketing-pack') ?></label>
            </th>

            <td>
                <select name="<?php echo HeurekaCzService::WIDGET_POSITION ?>"
                        id="<?php echo HeurekaCzService::WIDGET_POSITION ?>"
                        data-mmp-check-field="heureka-verified-widget-cz">
                    <option
                        <?php if ($heurekaCzService->getWidgetPosition() === BaseHeurekaService::POSITION_LEFT){ ?>selected="selected"<?php } ?>
                        value="<?php echo BaseHeurekaService::POSITION_LEFT ?>"><?php echo __('Left', 'mergado-marketing-pack') ?></option>
                    <option
                        <?php if ($heurekaCzService->getWidgetPosition() === BaseHeurekaService::POSITION_RIGHT){ ?>selected="selected"<?php } ?>
                        value="<?php echo BaseHeurekaService::POSITION_RIGHT ?>"><?php echo __('Right', 'mergado-marketing-pack') ?></option>
                </select>
            </td>
        </tr>


        <!--   Verified by customers - WIDGET - MARGIN TOP  -->
        <tr>
            <th>
                <label for="<?php echo HeurekaCzService::WIDGET_TOP_MARGIN ?>"><?php echo __('Widget top margin', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo HeurekaCzService::WIDGET_TOP_MARGIN ?>"
                       name="<?php echo HeurekaCzService::WIDGET_TOP_MARGIN ?>"
                       data-mmp-check-field="heureka-verified-widget-cz"
                       placeholder="<?php echo __('Top margin', 'mergado-marketing-pack') ?>"
                       value="<?php echo $heurekaCzService->getWidgetTopMargin() ?>"> px
            </td>
        </tr>


        <?php /*
            <!--   Verified by customers - WIDGET - SHOW ON MOBILE  -->
            <tr>
                <th>
                    <label for="heureka-widget-cz-show-mobile"><?php echo __('Show widget on mobile', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="checkbox" id="heureka-widget-cz-show-mobile" name="heureka-widget-cz-show-mobile" data-mmp-check-field="heureka-verified-widget-cz"
                           <?php if (get_option(HeurekaCzService::WIDGET_SHOW_MOBILE, 0) == 1){ ?>checked="checked"<?php } ?>>
                    <br><small class="badge badge_info"><?php echo __('If this option is enabled, the widget will appear on mobile devices regardless of the width setting for hiding the widget.', 'mergado-marketing-pack') ?></small>
                </td>
            </tr>


            <!--   Verified by customers - WIDGET - HIDE ON SMALLER SCREEN THAN  -->
            <tr>
                <th>
                    <label for="heureka-widget-cz-hide-width"><?php echo __('Hide on screens smaller than', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="text" id="heureka-widget-cz-hide-width" name="heureka-widget-cz-hide-width" data-mmp-check-field="heureka-verified-widget-cz"
                           placeholder="<?php echo __('Min. width to show', 'mergado-marketing-pack') ?>"
                           value="<?php echo get_option(HeurekaCzService::WIDGET_HIDE_WIDTH, ''); ?>"> px
                    <br><small class="badge badge_info"><?php echo __('The setting to hide the widget below a certain screen width (in px) is only valid for desktops. On mobile devices, this setting is ignored.', 'mergado-marketing-pack') ?></small>
                </td>
            </tr>

 */ ?>

        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php echo __('Save', 'mergado-marketing-pack') ?>"
               name="submit-save">
    </p>
</div>


<div class="card full">
    <h3><?php echo __('Heureka.cz : Conversions tracking', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>

        <!--   Order tracking - ENABLER   -->
        <tr>
            <th>
                <label for="<?php echo HeurekaCzService::CONVERSION_ACTIVE ?>"><?php echo __('Track conversions', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox"
                       id="<?php echo HeurekaCzService::CONVERSION_ACTIVE ?>"
                       name="<?php echo HeurekaCzService::CONVERSION_ACTIVE ?>"
                       data-mmp-check-main="heureka-track-cz"
                       data-mmp-activity-check-checkbox="true"
                       <?php if ($heurekaCzService->getConversionActive() === 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>

        <!--   Order tracking - CODE   -->
        <tr>
            <th>
                <label for="<?php echo HeurekaCzService::CONVERSION_CODE ?>"><?php echo __('Conversions code', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo HeurekaCzService::CONVERSION_CODE ?>"
                       name="<?php echo HeurekaCzService::CONVERSION_CODE ?>" data-mmp-check-field="heureka-track-cz"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $heurekaCzService->getConversionCode() ?>">
                <br><small
                        class="badge badge_question"><?php echo __('You can find your store conversion tracking key in the Heureka account administration under the Statistics and Reports > Conversion Tracking > Public Key for Conversion Tracking Code.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo HeurekaCzService::CONVERSION_VAT_INCL ?>"><?php echo __('With VAT', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="<?php echo HeurekaCzService::CONVERSION_VAT_INCL ?>"
                       name="<?php echo HeurekaCzService::CONVERSION_VAT_INCL ?>"
                       data-mmp-check-field="heureka-track-cz"
                       <?php if ($heurekaCzService->isConversionWithVat()){ ?>checked="checked"<?php } ?>>
                <br><small
                        class="badge badge_info"><?php echo __('Choose whether the conversion value will be sent with or without VAT. Note: In the specification of conversion tracking, Heureka recommends the price of the order and shipping to be including VAT.', 'mergado-marketing-pack') ?></small>
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

<!----------------------   SK   ---------------------->

<div class="card full">
    <h3><?php echo __('Heureka.sk : Verified by customers', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <!--   Verified by customers - ENABLER   -->
        <tr>
            <th>
                <label for="<?php echo HeurekaSkService::VERIFIED_ACTIVE ?>"><?php echo __('Enable verified by users', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox"
                       id="<?php echo HeurekaSkService::VERIFIED_ACTIVE ?>"
                       data-mmp-check-main="heureka-verified-sk"
                       name="<?php echo HeurekaSkService::VERIFIED_ACTIVE ?>"
                       data-mmp-activity-check-checkbox="true"
                       <?php if ($heurekaSkService->getVerifiedActive() === 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>

        <!--   Verified by customers - CODE   -->
        <tr>
            <th>
                <label for="<?php echo HeurekaSkService::VERIFIED_CODE ?>"><?php echo __('Verified by users code', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo HeurekaSkService::VERIFIED_CODE ?>"
                       name="<?php echo HeurekaSkService::VERIFIED_CODE ?>" data-mmp-check-field="heureka-verified-sk"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $heurekaSkService->getVerifiedCode() ?>">
                <br><small
                        class="badge badge_question"><?php echo __('You can find your store key in the Heureka account administration under Verified customers > Settings and questionnaire data > Secret Key for verified customers.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th>
                <label><?php echo __('Text of the customer\'s consent to sending the questionnaire', 'mergado-marketing-pack') ?></label>
            </th>
            <td>
                <a href="#mmp_consentText"
                   class="button button-primary button-large"><?php echo __('Edit consent text', 'mergado-marketing-pack') ?></a>
            </td>
        </tr>

        <!--   Verified by customers - WIDGET ENABLER   -->
        <tr>
            <th>
                <label for="<?php echo HeurekaSkService::WIDGET_ACTIVE ?>"><?php echo __('Enable sk widget', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox"
                       id="<?php echo HeurekaSkService::WIDGET_ACTIVE ?>"
                       name="<?php echo HeurekaSkService::WIDGET_ACTIVE ?>"
                       data-mmp-check-main="heureka-verified-widget-sk"
                       data-mmp-activity-check-checkbox="true"
                       <?php if ($heurekaSkService->getWidgetActive() === 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>


        <!--   Verified by customers - WIDGET - ID   -->
        <tr>
            <th>
                <label for="<?php echo HeurekaSkService::WIDGET_ID ?>"><?php echo __('Widget ID', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo HeurekaSkService::WIDGET_ID ?>"
                       name="<?php echo HeurekaSkService::WIDGET_ID ?>"
                       data-mmp-check-field="heureka-verified-widget-sk"
                       placeholder="<?php echo __('Insert Widget Id', 'mergado-marketing-pack') ?>"
                       value="<?php echo $heurekaSkService->getWidgetId(); ?>">
                <br><small class="badge badge_question"><?php echo __('The ID is the same as the Public Key for conversion tracking.
Or you can find the key of your widget in the Heureka account administration under the tab Verified customers > Settings and questionnaire data > Certificate icons Verified customers. The numeric code is in the embed code. It takes the form "... setKey\',\'330BD_YOUR_WIDGET_KEY_2A80\']); _ hwq.push\' ..."', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>


        <!--   Verified by customers - WIDGET - POSITION  -->
        <tr>
            <th>
                <label for="<?php echo HeurekaSkService::WIDGET_POSITION ?>"><?php echo __('Widget position', 'mergado-marketing-pack') ?></label>
            </th>
            <td>
                <select name="<?php echo HeurekaSkService::WIDGET_POSITION ?>"
                        id="<?php echo HeurekaSkService::WIDGET_POSITION ?>"
                        data-mmp-check-field="heureka-verified-widget-sk">
                    <option
                        <?php if ($heurekaSkService->getWidgetPosition() === BaseHeurekaService::POSITION_LEFT){ ?>selected="selected"<?php } ?>
                        value="<?php echo BaseHeurekaService::POSITION_LEFT ?>"><?php echo __('Left', 'mergado-marketing-pack') ?></option>
                    <option
                        <?php if ($heurekaSkService->getWidgetPosition() === BaseHeurekaService::POSITION_RIGHT){ ?>selected="selected"<?php } ?>
                        value="<?php echo BaseHeurekaService::POSITION_RIGHT ?>>"><?php echo __('Right', 'mergado-marketing-pack') ?></option>
                </select>

            </td>
        </tr>


        <!--   Verified by customers - WIDGET - MARGIN TOP  -->
        <tr>
            <th>
                <label for="<?php echo HeurekaSkService::WIDGET_TOP_MARGIN ?>"><?php echo __('Widget top margin', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo HeurekaSkService::WIDGET_TOP_MARGIN ?>"
                       name="<?php echo HeurekaSkService::WIDGET_TOP_MARGIN ?>"
                       data-mmp-check-field="heureka-verified-widget-sk"
                       placeholder="<?php echo __('Top margin', 'mergado-marketing-pack') ?>"
                       value="<?php echo $heurekaSkService->getWidgetTopMargin() ?>"> px
            </td>
        </tr>

        <?php

        /*
                    <!--   Verified by customers - WIDGET - SHOW ON MOBILE  -->
                    <tr>
                        <th>
                            <label for="heureka-widget-sk-show-mobile"><?php echo __('Show widget on mobile', 'mergado-marketing-pack') ?></label>
                        </th>
                        <td><input type="checkbox" id="heureka-widget-sk-show-mobile" name="heureka-widget-sk-show-mobile" data-mmp-check-field="heureka-verified-widget-sk"
                                   <?php if (get_option(HeurekaSkService::WIDGET_SHOW_MOBILE, 0) == 1){ ?>checked="checked"<?php } ?>>
                            <br><small class="badge badge_info"><?php echo __('If this option is enabled, the widget will appear on mobile devices regardless of the width setting for hiding the widget.', 'mergado-marketing-pack') ?></small>
                        </td>
                    </tr>

                    <!--   Verified by customers - WIDGET - HIDE ON SMALLER SCREEN THAN  -->
                    <tr>
                        <th>
                            <label for="heureka-widget-sk-hide-width"><?php echo __('Hide on screens smaller than', 'mergado-marketing-pack') ?></label>
                        </th>
                        <td><input type="text" id="heureka-widget-sk-hide-width" name="heureka-widget-sk-hide-width" data-mmp-check-field="heureka-verified-widget-sk"
                                   placeholder="<?php echo __('Min. width to show', 'mergado-marketing-pack') ?>"
                                   value="<?php echo get_option(HeurekaSkService::WIDGET_HIDE_WIDTH, ''); ?>"> px
                            <br><small class="badge badge_info"><?php echo __('The setting to hide the widget below a certain screen width (in px) is only valid for desktops. On mobile devices, this setting is ignored.', 'mergado-marketing-pack') ?></small>
                        </td>
                    </tr>

        */ ?>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php echo __('Save', 'mergado-marketing-pack') ?>"
               name="submit-save">
    </p>
</div>


<div class="card full">
    <h3><?php echo __('Heureka.sk : Conversions tracking', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <!--   Order tracking - ENABLER   -->
        <tr>
            <th>
                <label for="<?php echo HeurekaSkService::CONVERSION_ACTIVE ?>"><?php echo __('Track conversions', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox"
                       id="<?php echo HeurekaSkService::CONVERSION_ACTIVE ?>"
                       name="<?php echo HeurekaSkService::CONVERSION_ACTIVE ?>"
                       data-mmp-check-main="heureka-conversion-sk"
                       data-mmp-activity-check-checkbox="true"
                       <?php if ($heurekaSkService->getConversionActive() === 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>

        <!--   Order tracking - CODE   -->
        <tr>
            <th>
                <label for="<?php echo HeurekaSkService::CONVERSION_CODE ?>"><?php echo __('Conversions code', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo HeurekaSkService::CONVERSION_CODE ?>"
                       name="<?php echo HeurekaSkService::CONVERSION_CODE ?>"
                       data-mmp-check-field="heureka-conversion-sk"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $heurekaSkService->getConversionCode() ?>">
                <br><small
                        class="badge badge_question"><?php echo __('You can find your store conversion tracking key in the Heureka account administration under the Statistics and Reports > Conversion Tracking > Public Key for Conversion Tracking Code.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo HeurekaSkService::CONVERSION_VAT_INCL ?>"><?php echo __('With VAT', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="<?php echo HeurekaSkService::CONVERSION_VAT_INCL ?>"
                       name="<?php echo HeurekaSkService::CONVERSION_VAT_INCL ?>"
                       data-mmp-check-field="heureka-conversion-sk"
                       <?php if ($heurekaSkService->isConversionWithVat()){ ?>checked="checked"<?php } ?>>
                <br><small
                        class="badge badge_info"><?php echo __('Choose whether the conversion value will be sent with or without VAT. Note: In the specification of conversion tracking, Heureka recommends the price of the order and shipping to be including VAT.', 'mergado-marketing-pack') ?></small>
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

<!----------------------   OTHER   ---------------------->

<div class="card full" id="mmp_consentText">
    <h3><?php echo __('Heureka : Other settings', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th><strong><?php echo __('Edit text of consent', 'mergado-marketing-pack') ?></strong></th>
            <td>
                <small class="badge badge_question">
                    <?php echo __('Here you can edit the text of the sentence of consent to the sending of the questionnaire, displayed in the checkout page. This is an opt-out consent, ie the customer must confirm that he does not want to be involved in the program.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th>
                <label for="heureka-verify-opt-out-text-en_US"><?php echo __('en_US', 'mergado-marketing-pack') ?></label>
            </th>

            <?php
            $enUsValue = stripslashes(get_option('heureka-verify-opt-out-text-en_US'));
            $defaultValue = BaseHeurekaService::DEFAULT_OPT;

            if (trim($enUsValue) == '') {
                $enUsValue = $defaultValue;
            }
            ?>

            <td colspan="2"><textarea id="heureka-verify-opt-out-text-en_US" name="heureka-verify-opt-out-text-en_US"
                                      placeholder="<?php echo __('Insert your text for this language', 'mergado-marketing-pack') ?>"><?php echo $enUsValue ?></textarea>
                <br><small
                        class="badge badge_info"><?php echo __('English text will be used as default value if any other language won\'t be filled.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <?php foreach (get_available_languages() as $lang): ?>
            <tr>
                <th>
                    <label for="heureka-verify-opt-out-text-<?php echo $lang ?>"><?php echo __($lang, 'mergado-marketing-pack') ?></label>
                </th>
                <td colspan="2"><textarea id="heureka-verify-opt-out-text-<?php echo $lang ?>"
                                          name="heureka-verify-opt-out-text-<?php echo $lang ?>"
                                          placeholder="<?php echo __('Insert your text for this language', 'mergado-marketing-pack') ?>"><?php echo stripslashes(get_option('heureka-verify-opt-out-text-' . $lang)) ?></textarea>
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
