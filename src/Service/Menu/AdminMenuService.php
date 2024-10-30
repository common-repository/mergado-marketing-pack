<?php declare(strict_types=1);

namespace Mergado\Service\Menu;

use Mergado\Service\AlertService;
use Mergado\Traits\SingletonTrait;

class AdminMenuService
{
    use SingletonTrait;

    public function init(): void
    {
        $alertService = AlertService::getInstance();
        $errors = $alertService->getMergadoErrors();

        // Add main menu pages
        add_menu_page(
            __('Mergado Pack', 'mergado-marketing-pack'),
            $this->getMainItemText($errors),
            'manage_options',
            'mergado-config',
            ['Mergado_Marketing_Pack_Admin', 'display'],
            plugins_url('mergado-marketing-pack/admin/img/') . 'rsz_mergado_pack_logo_menu.png',
            58);

        // Add page to woocomerce
        add_submenu_page('woocommerce', __('Mergado Pack', 'mergado-marketing-pack'),
            $this->getMainItemText($errors),
            'manage_options', 'mergado-config', ['Mergado_Marketing_Pack_Admin', 'display']);

        // Add submenu pages
        add_submenu_page('mergado-config', __('Product feeds', 'mergado-marketing-pack'),
            $this->getProductFeedItemText($errors),
            'manage_options', 'mergado-feeds-product', ['Mergado_Marketing_Pack_Admin', 'display']);
        add_submenu_page('mergado-config', __('Other feeds', 'mergado-marketing-pack'),
            $this->getOthersFeedItemText($errors),
            'manage_options', 'mergado-feeds-other', ['Mergado_Marketing_Pack_Admin', 'display']);
        add_submenu_page('mergado-config', __('Ad Systems', 'mergado-marketing-pack'), __('Ad Systems', 'mergado-marketing-pack'), 'manage_options', 'mergado-adsys', array('Mergado_Marketing_Pack_Admin', 'display'));
        add_submenu_page('mergado-config',
            __('Cookies', 'mergado-marketing-pack'),
            __('Cookies', 'mergado-marketing-pack'),
            'manage_options',
            'mergado-cookies',
            ['Mergado_Marketing_Pack_Admin', 'display']);

        add_submenu_page('mergado-config', __('News', 'mergado-marketing-pack'), __('News', 'mergado-marketing-pack'), 'manage_options', 'mergado-news', array('Mergado_Marketing_Pack_Admin', 'display'));
        add_submenu_page('mergado-config', __('Support', 'mergado-marketing-pack'), __('Support', 'mergado-marketing-pack'), 'manage_options', 'mergado-support', array('Mergado_Marketing_Pack_Admin', 'display'));
        add_submenu_page('mergado-config', __('Licence', 'mergado-marketing-pack'), __('Licence', 'mergado-marketing-pack'), 'manage_options', 'mergado-licence', array('Mergado_Marketing_Pack_Admin', 'display'));
    }

    protected function getMainItemText(array $errors) : string
    {
        if ($errors['total'] == 0) {
            return __('Mergado Pack', 'mergado-marketing-pack');
        } else {
            return sprintf(__('Mergado <span class="awaiting-mod">%d</span>', 'mergado-marketing-pack'), $errors['total']);
        }
    }

    protected function getProductFeedItemText(array $errors) : string
    {
        if ($errors['product'] == 0) {
            return __('Product feeds', 'mergado-marketing-pack');
        } else {
            return sprintf(__('Product feeds <span class="awaiting-mod">%d</span>', 'mergado-marketing-pack'), $errors['product']);
        }
    }

    protected function getOthersFeedItemText(array $errors) : string
    {
        if ($errors['other'] == 0) {
            return __('Other feeds', 'mergado-marketing-pack');
        } else {
            return sprintf(__('Other feeds <span class="awaiting-mod">%d</span>', 'mergado-marketing-pack'), $errors['other']);
        }
    }
}
