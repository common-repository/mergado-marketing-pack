<?php

use Mergado\Service\CookieService;

$cookieService = CookieService::getInstance();
?>

<div class="card full mmp_cookiepage">
    <h2><?php echo __('Cookie consent settings', 'mergado-marketing-pack') ?></h2>
    <p><?php echo __('<strong>When you activate this feature, advertising scripts that use cookies will not automatically run unless consent is granted.</strong><br> Using this feature is at your own risk. The creator of module, the company Mergado technologies, LLC, is not liable for any losses or damages in any form.', 'mergado-marketing-pack') ?></p>

    <div style="display: flex; align-items: center; margin-top: 20px;">
        <input type="checkbox" id="<?php echo CookieService::FIELD_COOKIES_ENABLE ?>"
               name="<?php echo CookieService::FIELD_COOKIES_ENABLE ?>"
               <?php if ($cookieService->isCookieBlockingEnabled()){ ?>checked="checked"<?php } ?>>
        <label style="height: 23px; font-weight: 700;"
               for="<?php echo CookieService::FIELD_COOKIES_ENABLE ?>"><?php echo __('Activate cookie consent settings', 'mergado-marketing-pack') ?></label>
    </div>

    <h3 style="margin-top: 30px; margin-bottom: 10px;"><?php echo __('Functions by type of consent', 'mergado-marketing-pack') ?></h3>

    <div style="background-color: #f2f2f2; padding: 1px 15px 16px; border-radius: 4px; margin-bottom: 30px; border: 1px solid #c3c4c7; box-shadow: 0 1px 1px rgb(0 0 0 / 4%); margin-top: 10px;">
        <p><?php echo __('The functions are divided by consent type as follows:', 'mergado-marketing-pack') ?></p>
        <div>
            <strong><?php echo __('Advertisement:', 'mergado-marketing-pack') ?></strong> <?php echo __('Google Ads, Facebook Pixel, Heureka conversion tracking, Glami piXel, Sklik retargeting, Sklik conversion tracking, Zboží conversion tracking, Etarget, Najnakup.sk, Pricemania, Kelkoo conversion tracking, Biano Pixel', 'mergado-marketing-pack') ?>
        </div>
        <div>
            <strong><?php echo __('Analytics:', 'mergado-marketing-pack') ?></strong> <?php echo __('Google Analytics', 'mergado-marketing-pack') ?>
        </div>
        <div>
            <strong><?php echo __('Functional:', 'mergado-marketing-pack') ?></strong> <?php echo __('Google Customer Reviews, Heureka Verified by Customer', 'mergado-marketing-pack') ?>
        </div>

        <hr style="margin-top: 16px;">

        <p style="margin-bottom: 0;">
            <i><?php echo __('Google Tag Manager and other unlisted features are not dependent on consent.', 'mergado-marketing-pack') ?></i>
        </p>

    </div>

    <h3 style="margin-top: 30px;margin-bottom: 10px;"><?php echo __('CookieYes plugin support', 'mergado-marketing-pack') ?></h3>

    <p><?php echo __('If you have <strong>activated <a href="https://cs.wordpress.org/plugins/cookie-law-info/">CookieYes plugin</a></strong>, there is <strong>no need to set up anything.</strong>', 'mergado-marketing-pack') ?></p>

    <h3 style="margin-top: 30px;margin-bottom: 10px;"><?php echo __('Complianz plugin support', 'mergado-marketing-pack') ?></h3>

    <p><?php echo __('If you have <strong>activated</strong> <a href="https://cs.wordpress.org/plugins/complianz-gdpr/">Complianz – GDPR/CCPA Cookie Consent plugin</a>, there is <strong>no need to set up anything.</strong>', 'mergado-marketing-pack') ?></p>

    <h3 style="margin-top: 30px;margin-bottom: 10px;"><?php echo __('Cookiebot CMP plugin support', 'mergado-marketing-pack') ?></h3>

    <p><?php echo __('If you have <strong>activated</strong> <a href="https://cs.wordpress.org/plugins/cookiebot/">Cookiebot CMP plugin</a>, there is <strong>no need to set up anything.</strong>', 'mergado-marketing-pack') ?></p>

    <h3 style="margin-top: 30px;margin-bottom: 10px;"><?php echo __('Set cookie values manually', 'mergado-marketing-pack') ?></h3>

    <p><?php echo __('Manually type name of the cookie that corresponds to selected category.', 'mergado-marketing-pack') ?></p>
    <p><?php echo __('To activate scripts after change of user consent call javascript code <code>window.mmp.cookies.functions.checkAndSetCookies()</code> or reload the page.', 'mergado-marketing-pack') ?></p>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="<?php echo CookieService::FIELD_ANALYTICAL_USER ?>"><?php echo __('Analytics cookies', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo CookieService::FIELD_ANALYTICAL_USER ?>"
                       name="<?php echo CookieService::FIELD_ANALYTICAL_USER ?>"
                       style="width: 250px;"
                       placeholder="<?php echo __('Insert name of analytics cookie', 'mergado-marketing-pack') ?>"
                       value="<?php echo $cookieService->getAnalyticalCustomName() ?>">
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo CookieService::FIELD_ADVERTISEMENT_USER ?>"><?php echo __('Advertisement cookies', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo CookieService::FIELD_ADVERTISEMENT_USER ?>"
                       name="<?php echo CookieService::FIELD_ADVERTISEMENT_USER ?>"
                       style="width: 250px;"
                       placeholder="<?php echo __('Insert name of advertisement cookie', 'mergado-marketing-pack') ?>"
                       value="<?php echo $cookieService->getAdvertisementCustomName() ?>">
                <br>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo CookieService::FIELD_FUNCTIONAL_USER ?>"><?php echo __('Functional cookies', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo CookieService::FIELD_FUNCTIONAL_USER ?>"
                       name="<?php echo CookieService::FIELD_FUNCTIONAL_USER ?>"
                       style="width: 250px;"
                       placeholder="<?php echo __('Insert name of functional cookie', 'mergado-marketing-pack') ?>"
                       value="<?php echo $cookieService->getFunctionalCustomName() ?>">
                <br>
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
