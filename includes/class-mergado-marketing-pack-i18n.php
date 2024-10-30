<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.mergado.cz
 * @since      1.0.0
 *
 * @package    Mergado_Marketing_Pack
 * @subpackage Mergado_Marketing_Pack/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Mergado_Marketing_Pack
 * @subpackage Mergado_Marketing_Pack/includes
 * @author     Mergado technologies, s. r. o. <info@mergado.cz>
 */
class Mergado_Marketing_Pack_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'mergado-marketing-pack',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}
}
