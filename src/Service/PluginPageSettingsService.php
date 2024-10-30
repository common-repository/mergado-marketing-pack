<?php declare(strict_types=1);

namespace Mergado\Service;

use Mergado\Traits\SingletonTrait;

class PluginPageSettingsService
{
    use SingletonTrait;
    public function initSettings() : void
    {
        add_action( 'plugin_action_links_' . plugin_basename( __MERGADO_BASE_FILE__ ), [$this, 'getTemplate'] );
    }

    public function getTemplate(array $links) : array
    {
        // TODO: move to template and import here
        $links = array_merge( array(
            '<a href="' . esc_url( admin_url( 'admin.php?page=mergado-config' ) ) . '">' . __( 'Get started', 'textdomain' ) . '</a>',
            '<a href="' . esc_url( admin_url( 'admin.php?page=mergado-support' ) ) . '">' . __( 'Support', 'textdomain' ) . '</a>',
            '<a style="color: #7FBA2C !important; font-weight: 500;" href="' . esc_url( 'https://pack.mergado.com/woocommerce/?utm_source=mp&utm_medium=link&utm_campaign=official_webiste' ) . '">' . __( 'Official website', 'textdomain' ) . '</a>',
        ), $links );

        return $links;
    }
}
