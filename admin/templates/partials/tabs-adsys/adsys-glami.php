<?php

use Mergado\Service\External\Glami\GlamiPixelService;
use Mergado\Service\External\Glami\GlamiTopService;

$glamiPixelClass = GlamiPixelService::getInstance();
$glamiTopClass = GlamiTopService::getInstance();
?>


<div class="card full">
    <h3><?php echo __('Glami piXel', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th><label for="glami-form-active"><?php echo __('Active', 'mergado-marketing-pack') ?></label></th>
            <td><input type="checkbox" id="glami-form-active" name="glami-form-active"
                       data-mmp-check-main="glami-pixel-active" data-mmp-activity-check-checkbox="true"
                       <?php if ($glamiPixelClass->getActive() === 1){ ?>checked="checked"<?php } ?>>
            </td>
            <td>
                <small class="badge badge_question"><?php echo __('You can find your piXel in the Glami Administration at Glami piXel page > Implementing Glami piXel for Developers > Glami piXel Code section for YOUR ESHOP.', 'mergado-marketing-pack') ?></small>
            </td>
            <td></td>
        </tr>
        <tr>
            <th>
                <label for="glami-vat-included"><?php echo __('With VAT', 'mergado-marketing-pack') ?></label>
            </th>
            <td colspan="2"><input type="checkbox" id="glami-vat-included" name="glami-vat-included"
                                   data-mmp-check-field="glami-pixel-active"
                                   <?php if ($glamiPixelClass->isConversionWithVat()){ ?>checked="checked"<?php } ?>>
                <br><small
                        class="badge badge_info"><?php echo __('Choose whether the conversion value will be sent with or without VAT.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <?php foreach (GlamiPixelService::LANGUAGES as $key => $lang):
            $codeName = GlamiPixelService::getCodeName($lang);
            $activeLangName = GlamiPixelService::getActiveLangName($lang);
            ?>
            <tr>
                <th>
                    <label for="<?php echo $codeName ?>"><?php echo __('Pixel code', 'mergado-marketing-pack');
                        echo ' ' . $lang ?></label></th>
                <td class="glami-ECO">
                    <label for="<?php echo $activeLangName ?>"><?php echo $lang ?></label>
                    <input type="checkbox" id="<?php echo $activeLangName ?>" name="<?php echo $activeLangName ?>"
                           data-mmp-check-field="glami-pixel-active"
                           data-mmp-check-main="glami-pixel-<?php echo $lang ?>"
                           <?php if ($glamiPixelClass->getActiveLang($lang) === 1): ?>checked="checked" <?php endif ?>/>
                </td>
                <td><input type="text" id="<?php echo $codeName ?>"
                           name="<?php echo $codeName ?>"
                           placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                           value="<?php echo $glamiPixelClass->getCode($lang); ?>"
                           data-mmp-check-field="glami-pixel-<?php echo $lang ?>">
                </td>
                <td>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php echo __('Save', 'mergado-marketing-pack') ?>" name="submit-save">
    </p>
</div>

<div class="card full">
    <h3><?php echo __('Glami TOP', 'mergado-marketing-pack') ?></h3>

    <ul>
        <li>
            - <?php echo __('Your website must have HTTPS protocol on order confirmation page', 'mergado-marketing-pack') ?></li>
        <li>- <?php echo __('You have to set your DNS before use', 'mergado-marketing-pack') ?></li>
        <li>-
            <a href="https://www.glami.cz/info/reviews/implementation/"><?php echo __('Read more', 'mergado-marketing-pack') ?></a>
        </li>
    </ul>
    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th><label for="glami-top-form-active"><?php echo __('Active', 'mergado-marketing-pack') ?></label></th>
            <td><input type="checkbox" id="glami-top-form-active" name="glami-top-form-active"
                       data-mmp-check-main="glami-top" data-mmp-activity-check-checkbox="true"
                       <?php if ($glamiTopClass->getActive() === 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo GlamiTopService::SELECTION ?>"><?php echo __('Glami website', 'mergado-marketing-pack'); ?></label>
            </th>
            <td>
                <select name="<?php echo GlamiTopService::SELECTION ?>" id="<?php echo GlamiTopService::SELECTION ?>"
                        data-mmp-check-field="glami-top">

                    <?php foreach (GlamiTopService::LANGUAGES as $key => $data): ?>
                        <option
                            <?php if (count($glamiTopClass->getSelection()) > 0 && $glamiTopClass->getSelection()['id_option'] == $data['id_option']){ ?>selected="selected"<?php } ?>
                            value="<?php echo $data['id_option'] ?>"><?php echo $data['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo GlamiTopService::CODE ?>"><?php echo __('Glami TOP code', 'mergado-marketing-pack'); ?></label>
            </th>
            <td>
                <input type="text" id="<?php echo GlamiTopService::CODE ?>" name="<?php echo GlamiTopService::CODE ?>"
                       data-mmp-check-field="glami-top"
                       placeholder="<?php echo __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $glamiTopClass->getCode() ?>">
                <br><small
                        class="badge badge_question"><?php echo __('You can find your Glami TOP API key in the Glami Administration at the Glami TOP page > Implementation > Developer Implementation Guide> Javascript Integration section.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php echo __('Save', 'mergado-marketing-pack') ?>" name="submit-save">
    </p>
</div>
